package digirent.jpa;

import static org.junit.Assert.*;

import org.junit.AfterClass;
import org.junit.Test;

import javax.persistence.EntityManager;
import javax.persistence.EntityManagerFactory;

import java.util.Map;

import com.google.common.base.Optional;

import static digirent.util.ParameterUtils.params;

public class JPAStorageTest {
    private static final EntityManagerFactory ef = JPA.ef("test");

    @AfterClass
    public static void tearDown() {
        ef.close();
    }

    @Test
    public void testCRUD() {
        EntityManager em = ef.createEntityManager();

        em.getTransaction().begin();
        em.createNativeQuery("TRUNCATE TABLE session_storage").executeUpdate();
        em.getTransaction().commit();

        JPAStorage storage = new JPAStorage(ef, "session_storage");

        Optional<Map<String, Object>> vOpt;
        Map<String, Object> m;

        vOpt = storage.read("deadbeef");
        assertFalse(vOpt.isPresent());

        String key = storage.create(params("a", "1", "b", "2"));
        vOpt = storage.read(key);
        assertTrue(vOpt.isPresent());
        m = vOpt.get();
        assertEquals("1", m.get("a"));
        assertEquals("2", m.get("b"));

        storage.write(key, params("foo", 123));
        vOpt = storage.read(key);
        assertTrue(vOpt.isPresent());
        m = vOpt.get();
        assertEquals(123, m.get("foo"));

        storage.delete(key);
        vOpt = storage.read(key);
        assertFalse(vOpt.isPresent());

        em.close();
    }

    @Test
    public void testPrefix() {
        EntityManager em = ef.createEntityManager();

        em.getTransaction().begin();
        em.createNativeQuery("TRUNCATE TABLE session_storage").executeUpdate();
        em.getTransaction().commit();
        em.close();

        JPAStorage storage = new JPAStorage(ef, "session_storage", "user-");
        String key1 = storage.create(params());
        assertTrue(key1.startsWith("user-"));
    }
}
