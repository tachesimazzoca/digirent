package app.controllers;

import app.models.Account;
import app.models.AccountDao;
import app.models.AccountsSigninForm;
import app.models.AccountsSignupForm;
import app.models.UserHelper;
import com.google.common.base.Optional;
import digirent.config.Config;
import digirent.jersey.inject.Component;
import digirent.mail.MailerException;
import digirent.mail.TextMailerFactory;
import digirent.storage.Storage;
import digirent.view.View;
import digirent.view.helper.FormHelper;

import javax.validation.ConstraintViolation;
import javax.validation.Validator;
import javax.ws.rs.*;
import javax.ws.rs.core.*;
import java.util.Map;
import java.util.Set;

import static digirent.jersey.util.URIUtils.safeURI;
import static digirent.util.ParameterUtils.params;

@Path("/accounts")
@Produces(MediaType.TEXT_HTML)
public class AccountsController {
    @Context
    Config config;

    private final Validator validator;
    private final AccountDao accountDao;
    private final Storage<Map<String, Object>> signUpStorage;
    private final TextMailerFactory signUpMailerFactory;

    public AccountsController(
            Validator validator,
            AccountDao accountDao,
            Storage<Map<String, Object>> signUpStorage,
            TextMailerFactory signUpMailerFactory) {
        this.validator = validator;
        this.accountDao = accountDao;
        this.signUpStorage = signUpStorage;
        this.signUpMailerFactory = signUpMailerFactory;
    }

    @GET
    @Path("errors/{name}")
    public Response errors(@PathParam("name") String name) {
        return Response.status(Response.Status.FORBIDDEN)
                .entity(new View("accounts/errors/" + name))
                .build();
    }

    @GET
    @Path("entry")
    public Response entry(@Component UserHelper userHelper) {
        userHelper.logout();
        View view = new View("accounts/entry", params(
                "form", new FormHelper<AccountsSignupForm>(AccountsSignupForm.defaultForm())));
        return Response.ok(view).cookie(userHelper.toCookie()).build();
    }

    @POST
    @Path("entry")
    @Consumes("application/x-www-form-urlencoded")
    public Response postEntry(
            @Component UserHelper userHelper,
            @Context UriInfo uriInfo,
            MultivaluedMap<String, String> formParams)
            throws MailerException {

        userHelper.logout();

        AccountsSignupForm form = AccountsSignupForm.bindFrom(formParams);
        if (validator.validateProperty(form, "email").isEmpty()) {
            if (!form.getEmail().isEmpty()) {
                if (accountDao.findByEmail(form.getEmail()).isPresent()) {
                    form.setUniqueEmail(false);
                }
            }
        }
        Set<ConstraintViolation<AccountsSignupForm>> errors = validator.validate(form);
        if (!errors.isEmpty()) {
            View view = new View("accounts/entry", params(
                    "form", new FormHelper<AccountsSignupForm>(form, errors)));
            return Response.status(Response.Status.FORBIDDEN).entity(view)
                    .cookie(userHelper.toCookie()).build();
        }

        Map<String, Object> params = params(
                "email", form.getEmail(),
                "password", form.getPassword());
        String code = signUpStorage.create(params);
        String url = uriInfo.getBaseUriBuilder()
                .path("/accounts/activate")
                .queryParam("code", code)
                .build()
                .toString();
        signUpMailerFactory.create(form.getEmail(), url).send();

        return Response.ok(new View("accounts/verify"))
                .cookie(userHelper.toCookie()).build();
    }

    @GET
    @Path("activate")
    public Response activate(
            @Component UserHelper userHelper,
            @Context UriInfo uriInfo,
            @QueryParam("code") String code) {

        userHelper.logout();

        Optional<Map<String, Object>> opt = signUpStorage.read(code);
        signUpStorage.delete(code);
        if (!opt.isPresent()) {
            return Response.seeOther(uriInfo.getBaseUriBuilder()
                    .path("/accounts/errors/session").build())
                    .cookie(userHelper.toCookie()).build();
        }

        Map<String, Object> params = opt.get();
        if (accountDao.findByEmail((String) params.get("email")).isPresent()) {
            return Response.seeOther(uriInfo.getBaseUriBuilder()
                    .path("/accounts/errors/email").build())
                    .cookie(userHelper.toCookie()).build();
        }

        Account account = new Account();
        account.setEmail((String) params.get("email"));
        account.setStatus(Account.Status.ACTIVE);
        account.refreshPassword((String) params.get("password"));
        Account savedAccount = accountDao.save(account);
        return Response.ok(new View("accounts/activate",
                params("account", savedAccount)))
                .cookie(userHelper.toCookie()).build();
    }

    @GET
    @Path("login")
    public Response login(
            @Component UserHelper userHelper,
            @QueryParam("returnTo") @DefaultValue("") String returnTo) {

        userHelper.logout();

        AccountsSigninForm form = AccountsSigninForm.defaultForm();
        form.setReturnTo(returnTo);
        return Response.ok(new View("accounts/login", params(
                "form", new FormHelper<AccountsSigninForm>(form))))
                .cookie(userHelper.toCookie()).build();
    }

    @POST
    @Path("login")
    @Consumes("application/x-www-form-urlencoded")
    public Response postLogin(
            @Component UserHelper userHelper,
            @Context UriInfo uriInfo,
            MultivaluedMap<String, String> formParams) {

        userHelper.logout();

        AccountsSigninForm form = AccountsSigninForm.bindFrom(formParams);
        Set<ConstraintViolation<AccountsSigninForm>> errors = validator.validate(form);

        Account account = null;
        if (errors.isEmpty()) {
            Optional<Account> accountOpt = userHelper.authenticate(
                    form.getEmail(), form.getPassword());
            if (accountOpt.isPresent()) {
                account = accountOpt.get();
            } else {
                form.setAuthorized(false);
                errors = validator.validate(form);
            }
        }
        if (account == null) {
            View view = new View("accounts/login", params(
                    "form", new FormHelper<AccountsSigninForm>(form, errors)));
            return Response.status(Response.Status.FORBIDDEN).entity(view)
                    .cookie(userHelper.toCookie()).build();
        }

        String returnTo = form.getReturnTo();
        if (!returnTo.startsWith("/") || returnTo.isEmpty())
            returnTo = config.get("url.home", String.class);

        return Response.seeOther(safeURI(uriInfo, returnTo))
                .cookie(userHelper.toCookie()).build();
    }

    @GET
    @Path("logout")
    public Response logout(
            @Component UserHelper userHelper,
            @Context UriInfo uriInfo) {
        userHelper.logout();
        String returnTo = config.get("url.home", String.class);
        return Response.seeOther(safeURI(uriInfo, returnTo))
                .cookie(userHelper.toCookie()).build();
    }
}
