import React from 'react';
import { View, Text, StyleSheet, Image, TouchableOpacity, ScrollView } from 'react-native';
import { Ionicons, Feather } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';

const GestaoProdutosScreen = ({ navigation }) => {
  const produtos = []; 
  const favoritos = [];

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

  const renderImagem = (item) => {
    if (item?.imagem) {
      return <Image source={item.imagem} style={styles.produtoImagem} resizeMode="contain" />;
    }
    return <Text style={styles.semImagemTexto}>Nenhum produto disponível</Text>;
  };

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

        {/* Seção de Produtos */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Seus Produtos</Text>
          <View style={styles.card}>
            <TouchableOpacity>
              <Ionicons name="chevron-back" size={24} color="#333" />
            </TouchableOpacity>
            <View style={styles.imageContainer}>
              {renderImagem(produtos[0])}
            </View>
            <TouchableOpacity>
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

        {/* Seção de Favoritos */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Favoritos</Text>
          <View style={styles.card}>
            <TouchableOpacity>
              <Ionicons name="chevron-back" size={24} color="#333" />
            </TouchableOpacity>
            <View style={styles.imageContainer}>
              {renderImagem(favoritos[0])}
            </View>
            <TouchableOpacity>
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
    height: 150,
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
  },
  produtoImagem: {
    width: 100,
    height: 100,
  },
  semImagemTexto: {
    color: '#888',
    fontStyle: 'italic',
    textAlign: 'center',
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
});

export default GestaoProdutosScreen;