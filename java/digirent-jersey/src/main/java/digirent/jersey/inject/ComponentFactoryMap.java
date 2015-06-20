package digirent.jersey.inject;

import java.util.HashMap;
import java.util.Map;

public class ComponentFactoryMap {
    private Map<Class<?>, ComponentFactory<?>> factoryMap;

    public ComponentFactoryMap(ComponentFactory<?>... factories) {
        factoryMap = new HashMap<Class<?>, ComponentFactory<?>>();
        for (ComponentFactory<?> factory : factories) {
            factoryMap.put(factory.getGeneratedClass(), factory);
        }
    }

    public ComponentFactory<?> get(Class<?> key) {
        return factoryMap.get(key);
    }
}
