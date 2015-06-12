package digirent.config;

import com.google.common.base.Optional;
import org.junit.Test;

import static org.junit.Assert.*;

public class TypesafeConfigTest {
    @Test
    public void testMaybe() {
        Config config = TypesafeConfig.load("/test/conf/test.conf");
        assertEquals(Optional.absent(), config.maybe("unknown"));
        assertEquals("/", config.maybe("url.base").or("/foo"));
        assertEquals("TEST", config.maybe("session.cookieName").or("TEST"));
    }
}
