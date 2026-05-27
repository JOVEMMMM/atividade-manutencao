import org.junit.Before;
import org.junit.Test;
import static org.junit.Assert.*;
import java.util.Map;

public class BookManagerTest {
    private BookManager bookManager;
    
    
    @Before 
    public void setUp() {
        bookManager = new BookManager();
        LegacyDatabase.getBooks().clear(); // Esta linha garante que o banco inicie LIMPO antes de cada teste
    }
    
    @Test
    public void testarListagemSimplesDeLivrosComSucessoQuandoNãoVazia() {
        // Adicionamos um livro para que a lista NÃO esteja vazia
        //LegacyDatabase.addBookData("Clean Code", "Robert C. Martin", 2008, "Software", 3, 3, "A1", "ISBN-111");

        // O método deve rodar perfeitamente sem crashar caso tenha algum item na lista, se não tiver, ele crasha.
        bookManager.listBooksSimple();
    }

    @Test
    public void deveTestarAtualizacaoDeCopiasDisponiveisComRegraLegada() {
        int id = bookManager.registerBook("Design Patterns", "Erich Gamma", 1994, "Tecnologia", 10, 5, "C3", "ISBN-222");
        bookManager.updateAvailableWithLegacyRule(id, 3, 2, "proc-01", "manager-01", 0, "Aumento de estoque");

        Map<String, Object> dadosLivro = bookManager.findById(id);
        int copiasDisponiveis = ((Integer) dadosLivro.get("availableCopies")).intValue();

        assertEquals("O total de cópias disponíveis deveria ser 8 após a atualização.", 8, copiasDisponiveis);
    }
}
