package app;

import app.controllers.*;
import app.models.*;
import app.resources.UploadResource;
import app.util.Jackson;
import digirent.config.Config;
import digirent.config.TypesafeConfig;
import digirent.jersey.binder.ConfigBinder;
import digirent.jersey.inject.UserContextFactoryMap;
import digirent.jersey.inject.UserContextFactoryProvider;
import digirent.jersey.provider.ViewMessageBodyWriter;
import digirent.jpa.JPAStorage;
import digirent.mail.TextMailerFactory;
import digirent.storage.Storage;
import digirent.view.renderer.FreemarkerRenderer;
import digirent.view.renderer.Renderer;
import org.glassfish.jersey.media.multipart.MultiPartFeature;
import org.glassfish.jersey.server.ResourceConfig;

import javax.persistence.EntityManagerFactory;
import javax.persistence.Persistence;
import javax.validation.Validation;
import javax.validation.Validator;
import java.io.IOException;
import java.util.Map;

import static digirent.util.ParameterUtils.params;

public class AppResourceConfig extends ResourceConfig {
    public AppResourceConfig() throws IOException {
        // features
        register(MultiPartFeature.class);

        // factory
        AppFactoryConfig factoryConfig = Jackson.fromYAML(
                getClass().getResourceAsStream("/conf/factory.yml"),
                AppFactoryConfig.class);

        // config
        Config config = TypesafeConfig.load("/conf/application.conf");
        register(new ConfigBinder(config));

        // view
        String templateDir = this.getClass().getResource("/views/freemarker").getPath();
        Map<String, Object> sharedVariables = params("config", config);
        Renderer renderer = new FreemarkerRenderer(templateDir, sharedVariables);
        register(new ViewMessageBodyWriter(renderer));

        // storage
        EntityManagerFactory ef = Persistence.createEntityManagerFactory("default");
        Storage<Map<String, Object>> userStorage =
                new JPAStorage(ef, "session_storage", "user-");
        Storage<Map<String, Object>> signUpStorage =
                new JPAStorage(ef, "session_storage", "sign_up-");
        Storage<Map<String, Object>> recoveryStorage =
                new JPAStorage(ef, "session_storage", "recovery-");
        Storage<Map<String, Object>> profileStorage =
                new JPAStorage(ef, "session_storage", "profile-");

        // validator
        Validator validator = Validation.buildDefaultValidatorFactory().getValidator();

        // dao
        AccountDao accountDao = new AccountDao(ef);
        QuestionDao questionDao = new QuestionDao(ef);
        AnswerDao answerDao = new AnswerDao(ef);
        AccountQuestionDao accountQuestionDao = new AccountQuestionDao(ef);
        AccountAnswerDao accountAnswerDao = new AccountAnswerDao(ef);

        // mailer
        TextMailerFactory signUpMailerFactory = factoryConfig.getSignUpMailerFactory();
        TextMailerFactory recoveryMailerFactory = factoryConfig.getRecoveryMailerFactory();
        TextMailerFactory profileMailerFactory = factoryConfig.getProfileMailerFactory();

        // providers
        UserContextFactoryMap factoryMap = new UserContextFactoryMap(
                new UserHelperFactory(accountDao, userStorage, "APP_SESSION"));
        register(new UserContextFactoryProvider.Binder(factoryMap));

        // finder
        String tmpPath = config.get("path.tmp", String.class);
        TempFileHelper tempFileHelper = new TempFileHelper(tmpPath);
        String uploadPath = config.get("path.upload", String.class);
        FileHelper accountsIconFinder = FileHelperFactory.createAccountsIconFinder(
                uploadPath + "/accounts/icon");

        // resources
        register(new UploadResource(tempFileHelper, accountsIconFinder));

        // controllers
        register(new PagesController());
        register(new AccountsController(validator, accountDao,
                signUpStorage, signUpMailerFactory));
        register(new RecoveryController(validator, accountDao,
                recoveryStorage, recoveryMailerFactory));
        register(new DashboardController(questionDao, answerDao));
        register(new ProfileController(validator, accountDao, tempFileHelper, accountsIconFinder,
                profileStorage, profileMailerFactory));
        register(new QuestionsController(validator, questionDao, answerDao,
                accountDao, accountQuestionDao));
        register(new AnswersController(validator, questionDao, answerDao, accountAnswerDao));
    }
}
