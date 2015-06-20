package app;

import digirent.mail.TextMailerFactory;

public class AppFactoryConfig {
    private TextMailerFactory signUpMailerFactory;
    private TextMailerFactory recoveryMailerFactory;
    private TextMailerFactory profileMailerFactory;

    public TextMailerFactory getSignUpMailerFactory() {
        return signUpMailerFactory;
    }

    public void setSignUpMailerFactory(TextMailerFactory signUpMailerFactory) {
        this.signUpMailerFactory = signUpMailerFactory;
    }

    public TextMailerFactory getRecoveryMailerFactory() {
        return recoveryMailerFactory;
    }

    public void setRecoveryMailerFactory(TextMailerFactory recoveryMailerFactory) {
        this.recoveryMailerFactory = recoveryMailerFactory;
    }

    public TextMailerFactory getProfileMailerFactory() {
        return profileMailerFactory;
    }

    public void setProfileMailerFactory(TextMailerFactory profileMailerFactory) {
        this.profileMailerFactory = profileMailerFactory;
    }
}
