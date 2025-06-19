import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  Image,
  ScrollView,
  Modal,
} from 'react-native';
import { Feather } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';

// Dados simulados do backend
const produtosExemplo = {
  'Garrafa de Água': { preco: 5.5, categoria: 'Bebida', destaque: true },
  'Brigadeiro': { preco: 3.2, categoria: 'Doce', destaque: false },
  'Coxinha': { preco: 7.8, categoria: 'Salgado', destaque: true },
  'Pão de Queijo': { preco: 2.5, categoria: 'Outro', destaque: false },
  'Refrigerante': { preco: 6.0, categoria: 'Bebida', destaque: true },
  'Bolo': { preco: 8.5, categoria: 'Doce', destaque: false },
};

const Autoatendimento = ({ navigation }) => {
  const [carrinhoVisivel, setCarrinhoVisivel] = useState(false);
  const [carrinho, setCarrinho] = useState({});
  const [filtroVisivel, setFiltroVisivel] = useState(false);
  const [categoriaSelecionada, setCategoriaSelecionada] = useState('');
  const [verDestaques, setVerDestaques] = useState(false);
  const [ordenarPorPreco, setOrdenarPorPreco] = useState(false);
  const [carrinhoMinimizado, setCarrinhoMinimizado] = useState(false);
  const [termoPesquisa, setTermoPesquisa] = useState('');
  const [formaPagamento, setFormaPagamento] = useState(null);

  const handleAdicionarItem = (nome) => {
    setCarrinho((prev) => ({
      ...prev,
      [nome]: (prev[nome] || 0) + 1,
    }));
    setCarrinhoVisivel(true);
    setCarrinhoMinimizado(false);
  };

  const handleRemoverItem = (nome) => {
    setCarrinho((prev) => {
      const novo = { ...prev };
      if (novo[nome] > 1) novo[nome]--;
      else delete novo[nome];
      return novo;
    });
  };

  const calcularTotal = () => {
    return Object.entries(carrinho).reduce((total, [item, quantidade]) => {
      const precoItem = produtosExemplo[item]?.preco || 0;
      return total + precoItem * quantidade;
    }, 0).toFixed(2);
  };

  const handleFinalizarCompra = () => {
    if (!formaPagamento) {
      alert('Selecione uma forma de pagamento');
      return;
    }
    alert(`Venda finalizada!\nTotal: R$ ${calcularTotal()}\nForma de pagamento: ${formaPagamento}`);
    setCarrinho({});
    setCarrinhoVisivel(false);
  };

  const filtrarEOrdenarProdutos = () => {
    let produtos = Object.entries(produtosExemplo);
    
    if (termoPesquisa) {
      produtos = produtos.filter(([nome]) => 
        nome.toLowerCase().includes(termoPesquisa.toLowerCase())
      );
    }
    
    if (verDestaques) {
      produtos = produtos.filter(([, data]) => data.destaque);
    }
    
    if (categoriaSelecionada) {
      produtos = produtos.filter(([, data]) => data.categoria === categoriaSelecionada);
    }
    
    if (ordenarPorPreco) {
      produtos.sort(([, a], [, b]) => a.preco - b.preco);
    }
    
    return produtos;
  };

  return (
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.container}>
      <ScrollView contentContainerStyle={styles.content}>
        <View style={styles.header}>
          <Image source={require('../../assets/images/logopaz.png')} style={styles.logo} />
          <TouchableOpacity onPress={() => navigation.navigate('ProfileScreen')}>
            <Feather name="user" size={24} color="black" />
          </TouchableOpacity>
        </View>

        <Text style={styles.title}>Sua Loja</Text>

        <View style={styles.searchContainer}>
          <Feather name="search" size={18} color="#999" />
          <TextInput
            style={styles.searchInput}
            placeholder="Buscar produto..."
            placeholderTextColor="#aaa"
            value={termoPesquisa}
            onChangeText={setTermoPesquisa}
            returnKeyType="search"
          />
          <TouchableOpacity onPress={() => setFiltroVisivel(true)}>
            <Feather name="filter" size={18} color="#999" />
          </TouchableOpacity>
        </View>

        <View style={styles.produtoGrid}>
          {filtrarEOrdenarProdutos().map(([nome, { preco }], index) => (
            <View key={index} style={styles.produtoCard}>
              <Text style={styles.produtoImagemTexto}>Imagem</Text>
              <Text style={styles.produtoNome}>{nome}</Text>
              <Text style={styles.precoTexto}>R$ {preco.toFixed(2)}</Text>
              <TouchableOpacity 
                style={styles.botaoAdd} 
                onPress={() => handleAdicionarItem(nome)}
              >
                <Text style={styles.botaoAddTexto}>+</Text>
              </TouchableOpacity>
            </View>
          ))}
        </View>
      </ScrollView>

      {/* Carrinho flutuante */}
      {carrinhoVisivel && !carrinhoMinimizado && (
        <View style={styles.carrinhoFlutuante}>
          <Text style={styles.carrinhoTitulo}>Carrinho</Text>
          
          {Object.entries(carrinho).map(([nome, quantidade]) => {
            const precoItem = produtosExemplo[nome]?.preco || 0;
            const totalItem = (precoItem * quantidade).toFixed(2);
            
            return (
              <View key={nome} style={styles.itemCarrinho}>
                <View style={styles.itemInfo}>
                  <Text style={styles.itemNome}>{nome}</Text>
                  <Text style={styles.itemPreco}>R$ {precoItem.toFixed(2)}</Text>
                </View>
                <View style={styles.controlesItem}>
                  <TouchableOpacity onPress={() => handleRemoverItem(nome)}>
                    <Text style={styles.botaoQuantidade}>-</Text>
                  </TouchableOpacity>
                  <Text style={styles.quantidade}>{quantidade}</Text>
                  <TouchableOpacity onPress={() => handleAdicionarItem(nome)}>
                    <Text style={styles.botaoQuantidade}>+</Text>
                  </TouchableOpacity>
                  <Text style={styles.totalItem}>R$ {totalItem}</Text>
                </View>
              </View>
            );
          })}

          <View style={styles.divider} />

          <Text style={styles.total}>Total: R$ {calcularTotal()}</Text>

          {/* Forma de pagamento */}
          <View style={styles.paymentContainer}>
            <Text style={styles.paymentTitle}>Forma de pagamento:</Text>
            
            <TouchableOpacity 
              style={[
                styles.paymentOption,
                formaPagamento === 'Cartão' && styles.selectedPayment
              ]} 
              onPress={() => setFormaPagamento('Cartão')}
            >
              <Feather name="credit-card" size={20} color="#333" />
              <Text style={styles.paymentText}>Cartão</Text>
              {formaPagamento === 'Cartão' && (
                <Feather name="check" size={20} color="#333" style={styles.paymentCheck} />
              )}
            </TouchableOpacity>
            
            <TouchableOpacity 
              style={[
                styles.paymentOption,
                formaPagamento === 'Pix' && styles.selectedPayment
              ]} 
              onPress={() => setFormaPagamento('Pix')}
            >
              <Feather name="dollar-sign" size={20} color="#333" />
              <Text style={styles.paymentText}>Pix</Text>
              {formaPagamento === 'Pix' && (
                <Feather name="check" size={20} color="#333" style={styles.paymentCheck} />
              )}
            </TouchableOpacity>
            
            <TouchableOpacity 
              style={[
                styles.paymentOption,
                formaPagamento === 'Dinheiro' && styles.selectedPayment
              ]} 
              onPress={() => setFormaPagamento('Dinheiro')}
            >
              <Feather name="money" size={20} color="#333" />
              <Text style={styles.paymentText}>Dinheiro</Text>
              {formaPagamento === 'Dinheiro' && (
                <Feather name="check" size={20} color="#333" style={styles.paymentCheck} />
              )}
            </TouchableOpacity>
          </View>

          {/* Botão Finalizar Compra */}
          <TouchableOpacity 
            style={styles.finalizarButton}
            onPress={handleFinalizarCompra}
          >
            <Text style={styles.finalizarButtonText}>Finalizar venda</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            style={styles.minimizarBotao} 
            onPress={() => setCarrinhoMinimizado(true)}
          >
            <Feather name="chevron-down" size={20} color="#333" />
          </TouchableOpacity>
        </View>
      )}

      {carrinhoMinimizado && (
        <TouchableOpacity 
          style={styles.botaoAbrirCarrinho} 
          onPress={() => setCarrinhoMinimizado(false)}
        >
          <Feather name="shopping-cart" size={24} color="#fff" />
          {Object.keys(carrinho).length > 0 && (
            <View style={styles.contadorCarrinho}>
              <Text style={styles.contadorTexto}>{Object.keys(carrinho).length}</Text>
            </View>
          )}
        </TouchableOpacity>
      )}

      {/* Modal de Filtros */}
      <Modal visible={filtroVisivel} transparent animationType="slide">
        <View style={styles.modalContainer}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitulo}>Filtros</Text>

            <Text style={styles.modalLabel}>Categoria</Text>
            {['', 'Doce', 'Bebida', 'Salgado', 'Outro'].map((cat) => (
              <TouchableOpacity 
                key={cat || 'todos'} 
                style={styles.categoriaItem}
                onPress={() => setCategoriaSelecionada(cat === categoriaSelecionada ? '' : cat)}
              >
                <Feather 
                  name={cat === categoriaSelecionada ? 'check-circle' : 'circle'} 
                  size={20} 
                  color="#333" 
                />
                <Text style={styles.categoriaTexto}>{cat || 'Todas'}</Text>
              </TouchableOpacity>
            ))}

            <View style={styles.switchRow}>
              <Text style={styles.switchLabel}>Ver apenas destaques</Text>
              <TouchableOpacity onPress={() => setVerDestaques((v) => !v)}>
                <Feather name={verDestaques ? 'check-square' : 'square'} size={24} color="#333" />
              </TouchableOpacity>
            </View>

            <View style={styles.switchRow}>
              <Text style={styles.switchLabel}>Ordenar por preço</Text>
              <TouchableOpacity onPress={() => setOrdenarPorPreco((v) => !v)}>
                <Feather name={ordenarPorPreco ? 'check-square' : 'square'} size={24} color="#333" />
              </TouchableOpacity>
            </View>

            <TouchableOpacity 
              style={styles.botaoFechar} 
              onPress={() => setFiltroVisivel(false)}
            >
              <Text style={styles.botaoFecharTexto}>Aplicar Filtros</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </LinearGradient>
  );
};

const styles = StyleSheet.create({
  container: { 
    flex: 1,
    backgroundColor: '#F5F5F5',
  },
  content: { 
    padding: 20,
    paddingBottom: 100,
  },
  header: { 
    flexDirection: 'row', 
    justifyContent: 'space-between', 
    alignItems: 'center',
    marginBottom: 20,
  },
  logo: { 
    width: 50, 
    height: 50, 
    resizeMode: 'contain' 
  },
  title: { 
    fontSize: 24, 
    fontWeight: 'bold', 
    marginBottom: 20,
    color: '#333',
  },
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#fff',
    padding: 12,
    borderRadius: 10,
    marginBottom: 20,
    shadowColor: '#000',
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  searchInput: { 
    flex: 1, 
    marginHorizontal: 10,
    fontSize: 16,
    color: '#333',
  },
  produtoGrid: { 
    flexDirection: 'row', 
    flexWrap: 'wrap', 
    justifyContent: 'space-between',
  },
  produtoCard: {
    width: '48%',
    backgroundColor: '#fff',
    padding: 15,
    borderRadius: 12,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
    marginBottom: 16,
    borderWidth: 1,
    borderColor: '#ccc',
  },
  produtoImagemTexto: { 
    marginBottom: 10, 
    color: '#888',
    fontSize: 14,
  },
  produtoNome: {
    fontWeight: 'bold',
    marginBottom: 6,
    textAlign: 'center',
    fontSize: 16,
    color: '#333',
  },
  precoTexto: { 
    marginBottom: 10,
    fontSize: 16,
    fontWeight: '600',
    color: '#333',
  },
  botaoAdd: {
    backgroundColor: '#333',
    width: '100%',
    padding: 8,
    borderRadius: 6,
    alignItems: 'center',
  },
  botaoAddTexto: { 
    color: '#fff', 
    fontWeight: 'bold',
    fontSize: 16,
  },
  carrinhoFlutuante: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    backgroundColor: '#fff',
    padding: 20,
    borderTopLeftRadius: 16,
    borderTopRightRadius: 16,
    shadowColor: '#000',
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 10,
    borderWidth: 1,
    borderColor: '#ccc',
  },
  carrinhoTitulo: { 
    fontSize: 18, 
    fontWeight: 'bold', 
    marginBottom: 15,
    color: '#333',
  },
  itemCarrinho: { 
    flexDirection: 'row', 
    justifyContent: 'space-between', 
    alignItems: 'center',
    marginVertical: 8,
  },
  itemInfo: {
    flex: 1,
  },
  itemNome: {
    fontSize: 16,
    color: '#333',
  },
  itemPreco: {
    fontSize: 14,
    color: '#555',
    marginTop: 2,
  },
  controlesItem: { 
    flexDirection: 'row', 
    alignItems: 'center', 
    gap: 12,
  },
  botaoQuantidade: { 
    fontSize: 20, 
    paddingHorizontal: 8,
    color: '#333',
  },
  quantidade: { 
    fontWeight: 'bold',
    fontSize: 16,
    color: '#333',
  },
  totalItem: {
    marginLeft: 15,
    fontWeight: 'bold',
    color: '#333',
  },
  divider: {
    height: 1,
    backgroundColor: '#ccc',
    marginVertical: 10,
  },
  total: { 
    fontSize: 18, 
    fontWeight: 'bold', 
    marginTop: 5,
    color: '#333',
    textAlign: 'right',
  },
  paymentContainer: {
    marginTop: 15,
  },
  paymentTitle: {
    fontWeight: 'bold',
    marginBottom: 8,
    color: '#333',
  },
  paymentOption: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 8,
    paddingHorizontal: 10,
    borderRadius: 6,
    backgroundColor: '#f5f5f5',
    marginBottom: 8,
  },
  selectedPayment: {
    backgroundColor: '#e0e0e0',
  },
  paymentText: {
    marginLeft: 10,
    color: '#333',
    flex: 1,
  },
  paymentCheck: {
    marginLeft: 10,
  },
  finalizarButton: {
    backgroundColor: '#333',
    padding: 14,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 15,
  },
  finalizarButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },
  minimizarBotao: { 
    alignSelf: 'center', 
    marginTop: 10,
    padding: 8,
  },
  botaoAbrirCarrinho: {
    position: 'absolute',
    bottom: 20,
    right: 20,
    backgroundColor: '#333',
    borderRadius: 30,
    width: 60,
    height: 60,
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: '#000',
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 8,
  },
  contadorCarrinho: {
    position: 'absolute',
    top: -5,
    right: -5,
    backgroundColor: '#666',
    borderRadius: 10,
    width: 20,
    height: 20,
    justifyContent: 'center',
    alignItems: 'center',
  },
  contadorTexto: {
    color: '#fff',
    fontSize: 12,
    fontWeight: 'bold',
  },
  modalContainer: {
    flex: 1,
    justifyContent: 'flex-end',
    backgroundColor: 'rgba(0,0,0,0.5)',
  },
  modalContent: {
    backgroundColor: '#fff',
    padding: 25,
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    borderWidth: 1,
    borderColor: '#ccc',
  },
  modalTitulo: { 
    fontSize: 20, 
    fontWeight: 'bold', 
    marginBottom: 20,
    color: '#333',
  },
  modalLabel: { 
    marginTop: 10, 
    fontWeight: 'bold',
    marginBottom: 10,
    color: '#555',
  },
  categoriaItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 8,
  },
  categoriaTexto: {
    marginLeft: 10,
    fontSize: 16,
    color: '#333',
  },
  switchRow: { 
    flexDirection: 'row', 
    justifyContent: 'space-between', 
    alignItems: 'center', 
    marginTop: 15,
    paddingVertical: 8,
  },
  switchLabel: {
    fontSize: 16,
    color: '#333',
  },
  botaoFechar: {
    backgroundColor: '#333',
    padding: 14,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 20,
  },
  botaoFecharTexto: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },
});

export default Autoatendimento;