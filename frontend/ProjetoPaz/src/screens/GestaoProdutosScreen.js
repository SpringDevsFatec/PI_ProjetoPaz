import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, Image, TouchableOpacity, ScrollView } from 'react-native';
import { Ionicons, Feather } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import api from '../services/api';

const GestaoProdutosScreen = ({ navigation }) => {
  const [produtos, setProdutos] = useState([]);
  const [favoritos, setFavoritos] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  
  // Estados para controlar os índices dos carrosseis
  const [currentProdutoIndex, setCurrentProdutoIndex] = useState(0);
  const [currentFavoritoIndex, setCurrentFavoritoIndex] = useState(0);

  useEffect(() => {
    fetchProducts();
  }, []);

  const fetchProducts = async () => {
    try {
      setLoading(true);
      const response = await api.get('/products/active');
      
      if (response.data.status) {
        setProdutos(response.data.content);
        // Filtra os produtos favoritos
        const favs = response.data.content.filter(prod => prod.is_favorite === "1");
        setFavoritos(favs);
      } else {
        setError(response.data.message || 'Erro ao buscar produtos');
      }
    } catch (err) {
      setError(err.message || 'Erro na conexão com o servidor');
    } finally {
      setLoading(false);
    }
  };

  const handleProfilePress = () => {
    navigation.navigate('ProfileScreen');
  };

  const handleHistoricoPress = () => {
    navigation.navigate('Historico');
  };

  const handleCadastroPress = () => {
    navigation.navigate('CadastroProduto'); 
  };

  const handlePedidosPress = () => {
    navigation.navigate('Pedido');
  };

const handleEditProdutoPress = (produto) => {
  navigation.navigate('EditarProduto', { 
    produtoId: produto.id,  // Certifique-se que está passando o ID correto
    produto: produto       // Passa o objeto completo se necessário
  });
};

  // Funções para navegação do carrossel de produtos
  const nextProduto = () => {
    if (produtos.length > 0) {
      setCurrentProdutoIndex((prevIndex) => 
        prevIndex === produtos.length - 1 ? 0 : prevIndex + 1
      );
    }
  };

  const prevProduto = () => {
    if (produtos.length > 0) {
      setCurrentProdutoIndex((prevIndex) => 
        prevIndex === 0 ? produtos.length - 1 : prevIndex - 1
      );
    }
  };

  // Funções para navegação do carrossel de favoritos
  const nextFavorito = () => {
    if (favoritos.length > 0) {
      setCurrentFavoritoIndex((prevIndex) => 
        prevIndex === favoritos.length - 1 ? 0 : prevIndex + 1
      );
    }
  };

  const prevFavorito = () => {
    if (favoritos.length > 0) {
      setCurrentFavoritoIndex((prevIndex) => 
        prevIndex === 0 ? favoritos.length - 1 : prevIndex - 1
      );
    }
  };

  const renderImagem = (item) => {
    if (item?.img_product) {
      return (
        <Image 
          source={{ uri: item.img_product }} 
          style={styles.produtoImagem} 
          resizeMode="cover"
          onError={() => console.log('Erro ao carregar imagem')}
        />
      );
    }
    return (
      <View style={styles.placeholderContainer}>
        <Ionicons name="image-outline" size={30} color="#ccc" />
        <Text style={styles.semImagemTexto}>Sem imagem</Text>
      </View>
    );
  };

      const renderProdutoInfo = (item) => {
        if (!item) return null;
        
        return (
          <View style={styles.produtoContainer}>
            <TouchableOpacity 
              onPress={() => handleEditProdutoPress(item)}
              style={styles.imagemWrapper}
            >
              {renderImagem(item)}
            </TouchableOpacity>
            <Text style={styles.produtoNome} numberOfLines={2}>{item.name}</Text>
            <Text style={styles.produtoPreco}>R$ {item.sale_price}</Text>
          </View>
        );
      };

  if (loading) {
    return (
      <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={[styles.gradient, styles.loadingContainer]}>
        <Text>Carregando produtos...</Text>
      </LinearGradient>
    );
  }

  if (error) {
    return (
      <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={[styles.gradient, styles.errorContainer]}>
        <Text style={styles.errorText}>{error}</Text>
        <TouchableOpacity onPress={fetchProducts} style={styles.retryButton}>
          <Text style={styles.retryButtonText}>Tentar novamente</Text>
        </TouchableOpacity>
      </LinearGradient>
    );
  }

  return (
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.gradient}>
      <ScrollView 
        contentContainerStyle={styles.container}
        showsVerticalScrollIndicator={false}
      >
        {/* Cabeçalho com ícone de usuário */}
        <View style={styles.header}>
          <Image 
            source={require('../../assets/images/logopaz.png')} 
            style={styles.logo} 
          />
          <TouchableOpacity onPress={handleProfilePress}>
            <Ionicons 
              name="person-outline" 
              size={28} 
              color="#333" 
              style={styles.profileIcon}
            />
          </TouchableOpacity>
        </View>

        {/* Título da Página */}
        <Text style={styles.pageTitle}>Gestão do Sistema</Text>

        {/* Nova Seção de Pedidos */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Pedidos Recentes</Text>
          <View style={styles.card}>
            <View style={styles.imageContainer}>
              <Text style={styles.semImagemTexto}>Nenhum pedido recente</Text>
            </View>
          </View>
          <TouchableOpacity 
            style={styles.pedidosBtn}
            onPress={() => navigation.navigate('VerPedidos')}
          >
            <Text style={styles.pedidosBtnText}>Ver Todos os Pedidos</Text>
            <Feather name="chevron-right" size={18} color="white" />
          </TouchableOpacity>
        </View>

        {/* Seção de Produtos com Carrossel Funcional */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Seus Produtos</Text>
          <View style={styles.card}>
            <TouchableOpacity 
              onPress={prevProduto}
              style={[styles.arrowButton, { opacity: produtos.length > 1 ? 1 : 0.3 }]}
              disabled={produtos.length <= 1}
            >
              <Ionicons name="chevron-back" size={24} color="#333" />
            </TouchableOpacity>
            
            <View style={styles.imageContainer}>
              {produtos.length > 0 ? (
                <>
                  {renderProdutoInfo(produtos[currentProdutoIndex])}
                  {produtos.length > 1 && (
                    <View style={styles.indicatorContainer}>
                      {produtos.map((_, index) => (
                        <View
                          key={index}
                          style={[
                            styles.indicator,
                            index === currentProdutoIndex && styles.activeIndicator
                          ]}
                        />
                      ))}
                    </View>
                  )}
                </>
              ) : (
                <Text style={styles.semImagemTexto}>Nenhum produto cadastrado</Text>
              )}
            </View>
            
            <TouchableOpacity 
              onPress={nextProduto}
              style={[styles.arrowButton, { opacity: produtos.length > 1 ? 1 : 0.3 }]}
              disabled={produtos.length <= 1}
            >
              <Ionicons name="chevron-forward" size={24} color="#333" />
            </TouchableOpacity>
          </View>
        </View>

        {/* Seção de Adicionar Produto */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Adicionar Produto</Text>
          <TouchableOpacity 
            style={[styles.card, styles.uploadCard]}
            onPress={handleCadastroPress}
          >
            <Ionicons name="cloud-upload-outline" size={40} color="#aaa" />
            <Text style={styles.uploadText}>Adicionar Produto</Text>
          </TouchableOpacity>
        </View>

        {/* Botão de Histórico */}
        <TouchableOpacity style={styles.histBtn} onPress={handleHistoricoPress}>
          <Text style={styles.histBtnText}>Histórico Geral</Text>
          <Feather name="chevron-right" size={18} color="white" />
        </TouchableOpacity>

        {/* Seção de Favoritos com Carrossel Funcional */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Favoritos</Text>
          <View style={styles.card}>
            <TouchableOpacity 
              onPress={prevFavorito}
              style={[styles.arrowButton, { opacity: favoritos.length > 1 ? 1 : 0.3 }]}
              disabled={favoritos.length <= 1}
            >
              <Ionicons name="chevron-back" size={24} color="#333" />
            </TouchableOpacity>
            
            <View style={styles.imageContainer}>
              {favoritos.length > 0 ? (
                <>
                  {renderProdutoInfo(favoritos[currentFavoritoIndex])}
                  {favoritos.length > 1 && (
                    <View style={styles.indicatorContainer}>
                      {favoritos.map((_, index) => (
                        <View
                          key={index}
                          style={[
                            styles.indicator,
                            index === currentFavoritoIndex && styles.activeIndicator
                          ]}
                        />
                      ))}
                    </View>
                  )}
                </>
              ) : (
                <Text style={styles.semImagemTexto}>Nenhum favorito</Text>
              )}
            </View>
            
            <TouchableOpacity 
              onPress={nextFavorito}
              style={[styles.arrowButton, { opacity: favoritos.length > 1 ? 1 : 0.3 }]}
              disabled={favoritos.length <= 1}
            >
              <Ionicons name="chevron-forward" size={24} color="#333" />
            </TouchableOpacity>
          </View>
        </View>
      </ScrollView>

      {/* Rodapé Fixo */}
      <View style={styles.footer}>
        <Ionicons name="home-outline" size={24} color="white" />
      </View>
    </LinearGradient>
  );
};

const styles = StyleSheet.create({
  gradient: {
    flex: 1,
  },
  container: {
    paddingTop: 40,
    paddingHorizontal: 20,
    paddingBottom: 100,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 20,
  },
  logo: {
    width: 70,
    height: 70,
    resizeMode: 'contain',
  },
  profileIcon: {
    padding: 8,
    backgroundColor: 'rgba(255,255,255,0.7)',
    borderRadius: 20,
  },
  pageTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#1a1a1a',
    textAlign: 'center',
    marginBottom: 20,
  },
  section: {
    marginBottom: 25,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 15,
    textAlign: 'center',
  },
  card: {
    borderWidth: 1,
    borderColor: '#ddd',
    borderRadius: 12,
    padding: 15,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    minHeight: 200, // Mudei de height para minHeight
    backgroundColor: 'rgba(255,255,255,0.8)',
  },
  uploadCard: {
    justifyContent: 'center',
    alignItems: 'center',
  },
  imageContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    maxHeight: 140, // Adicionei limite máximo
  },
  // Novo estilo para wrapper da imagem com ajuste automático e responsivo
  imagemWrapper: {
    width: 100,
    height: 100,
    borderRadius: 8,
    overflow: 'hidden', // Isso é essencial para cortar o que ultrapassar
    backgroundColor: '#f0f0f0',
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1,
    borderColor: '#e0e0e0',
    marginBottom: 8, // Espaço para o texto
  },
  produtoImagem: {
    width: '100%',
    height: '100%',
    backgroundColor: 'transparent',
  },
  placeholderContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    width: '100%',
    height: '100%',
  },
  semImagemTexto: {
    color: '#888',
    fontStyle: 'italic',
    textAlign: 'center',
    fontSize: 12,
  },
  uploadText: {
    marginTop: 10,
    color: '#555',
    fontSize: 14,
  },
  histBtn: {
    flexDirection: 'row',
    backgroundColor: '#333',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 20,
  },
  histBtnText: {
    color: '#fff',
    fontWeight: 'bold',
    marginRight: 5,
  },
  pedidosBtn: {
    flexDirection: 'row',
    backgroundColor: '#333',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 10,
    marginBottom: 20,
  },
  pedidosBtnText: {
    color: '#fff',
    fontWeight: 'bold',
    marginRight: 5,
  },
  footer: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    backgroundColor: '#333',
    paddingVertical: 15,
    alignItems: 'center',
    borderTopLeftRadius: 15,
    borderTopRightRadius: 15,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  errorText: {
    color: 'red',
    marginBottom: 20,
    textAlign: 'center',
  },
  retryButton: {
    backgroundColor: '#333',
    padding: 10,
    borderRadius: 5,
  },
  retryButtonText: {
    color: 'white',
  },
  produtoContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    width: '100%',
  },
  produtoNome: {
    marginTop: 8,
    fontWeight: 'bold',
    color: '#333',
    fontSize: 14,
    textAlign: 'center',
    maxWidth: 160,
  },
  produtoPreco: {
    color: 'green',
    fontWeight: 'bold',
    fontSize: 16,
    marginTop: 4,
  },
  // Novos estilos para o carrossel
  arrowButton: {
    padding: 8,
    borderRadius: 20,
    backgroundColor: 'rgba(255,255,255,0.8)',
  },
  indicatorContainer: {
    flexDirection: 'row',
    marginTop: 8,
    justifyContent: 'center',
  },
  indicator: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: '#ccc',
    marginHorizontal: 2,
  },
  activeIndicator: {
    backgroundColor: '#333',
  },
});

export default GestaoProdutosScreen;