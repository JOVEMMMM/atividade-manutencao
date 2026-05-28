import org.junit.Before;
import org.junit.Test;
import static org.junit.Assert.*;
import java.util.Map;

public class LegacyDatabaseTest {

    @Before
    public void setUp() {
        LegacyDatabase.books.clear();
        LegacyDatabase.users.clear();
        LegacyDatabase.logs.clear();
        
        LegacyDatabase.BOOK_SEQ = 1;
        LegacyDatabase.USER_SEQ = 1;
    }

    @Test
    public void devePreservarCategoriaSoftwareNosLivrosAposExecutarCargaInicial() {
        LegacyDatabase.seedInitialData();

        Map<String, Object> livro1 = LegacyDatabase.getBookById(1);
        Map<String, Object> livro2 = LegacyDatabase.getBookById(2);
        Map<String, Object> livro3 = LegacyDatabase.getBookById(3);

        assertNotNull("O livro 1 deveria ter sido inserido.", livro1);
        assertNotNull("O livro 2 deveria ter sido inserido.", livro2);
        assertNotNull("O livro 3 deveria ter sido inserido.", livro3);

        assertEquals("A categoria do livro 1 deve ser 'Software'.", "Software", livro1.get("category"));
        assertEquals("A categoria do livro 2 deve ser 'Software'.", "Software", livro2.get("category"));
        assertEquals("A categoria do livro 3 deve ser 'Software'.", "Software", livro3.get("category"));
    }
}