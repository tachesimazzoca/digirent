package digirent.jersey.inject;

import org.glassfish.jersey.server.internal.inject.AbstractContainerRequestValueFactory;

public abstract class ComponentFactory<T> extends AbstractContainerRequestValueFactory<T> {

    public abstract ComponentFactory clone();

    public abstract Class<T> getGeneratedClass();
}

