import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Image, Alert, ScrollView, ActivityIndicator } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons, MaterialIcons, Feather } from '@expo/vector-icons';
import { showImagePickerOptions } from '../utils/imageUtils';
import { cadastrarProdutoComImagem } from '../services/api';

const CadastroProdutoTela = ({ navigation }) => {
  // Estados do formulário
  const [nome, setNome] = useState('');
  const [preco, setPreco] = useState('');
  const [categoria, setCategoria] = useState('');
  const [tipo, setTipo] = useState('');
  const [fornecedor, setFornecedor] = useState('');
  const [isFavorito, setIsFavorito] = useState(false);
  const [imagemSelecionada, setImagemSelecionada] = useState(null);
  const [loading, setLoading] = useState(false);

  // Função para alternar favorito
  const toggleFavorito = () => {
    setIsFavorito(!isFavorito);
  };

  // Função para editar imagem
  const editarImagem = async () => {
    try {
      const resultado = await showImagePickerOptions();
      if (resultado) {
        setImagemSelecionada(resultado);
      }
    } catch (error) {
      Alert.alert('Erro', 'Falha ao selecionar imagem');
      console.error(error);
    }
  };

  // Função para cadastrar produto
  const cadastrarProduto = async () => {
    if (!nome || !preco || !categoria || !tipo) {
      Alert.alert('Atenção', 'Preencha todos os campos obrigatórios');
      return;
    }

    try {
      setLoading(true);

      const produto = {
        nome,
        preco,
        categoria,
        tipo,
        fornecedor,
        isFavorito,
        dataCadastro: new Date().toLocaleDateString(),
      };

      // Faz a requisição com ou sem imagem
      let response;
      if (imagemSelecionada) {
        response = await cadastrarProdutoComImagem(
          produto, 
          imagemSelecionada.base64, 
          imagemSelecionada.type
        );
      } else {
        response = await cadastrarProdutoComImagem(produto);
      }

      console.log('Produto cadastrado:', response.data);
      Alert.alert('Sucesso', 'Produto cadastrado com sucesso!');
      navigation.goBack();
    } catch (error) {
      console.error('Erro ao cadastrar produto:', error);
      Alert.alert('Erro', 'Falha ao cadastrar produto. Tente novamente.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.container}>
      {/* Cabeçalho */}
      <View style={styles.header}>
        <Image source={require('../../assets/images/logopaz.png')} style={styles.logo} />
        <Text style={styles.title}>Cadastrar Produto</Text>
      </View>

      {/* Formulário */}
      <ScrollView contentContainerStyle={styles.formContainer}>
        {/* Nome */}
        <Text style={styles.label}>Nome do Produto *</Text>
        <TextInput 
          style={styles.input} 
          placeholder="Ex: Água Mineral 500ml" 
          placeholderTextColor="#666"
          value={nome}
          onChangeText={setNome}
        />

        {/* Preço */}
        <Text style={styles.label}>Preço *</Text>
        <TextInput 
          style={styles.input} 
          placeholder="R$ 0,00" 
          placeholderTextColor="#666"
          keyboardType="numeric"
          value={preco}
          onChangeText={setPreco}
        />

        {/* Categoria */}
        <Text style={styles.label}>Categoria *</Text>
        <TextInput 
          style={styles.input} 
          placeholder="Ex: Bebidas" 
          placeholderTextColor="#666"
          value={categoria}
          onChangeText={setCategoria}
        />

        {/* Tipo */}
        <Text style={styles.label}>Tipo *</Text>
        <View style={styles.radioContainer}>
          <TouchableOpacity 
            style={[styles.radioButton, tipo === 'Doação' && styles.radioSelected]}
            onPress={() => setTipo('Doação')}
          >
            {tipo === 'Doação' && <View style={styles.radioInner} />}
          </TouchableOpacity>
          <Text style={styles.radioText}>Doação</Text>

          <TouchableOpacity 
            style={[styles.radioButton, tipo === 'Compra' && styles.radioSelected]}
            onPress={() => setTipo('Compra')}
          >
            {tipo === 'Compra' && <View style={styles.radioInner} />}
          </TouchableOpacity>
          <Text style={styles.radioText}>Compra</Text>
        </View>

        {/* Fornecedor */}
        <Text style={styles.label}>Fornecedor</Text>
        <TextInput 
          style={styles.input} 
          placeholder="Digite o nome do fornecedor" 
          placeholderTextColor="#666"
          value={fornecedor}
          onChangeText={setFornecedor}
        />

        {/* Imagem */}
        <Text style={styles.label}>Imagem do Produto</Text>
        <TouchableOpacity style={styles.imageUploadContainer} onPress={editarImagem}>
          {imagemSelecionada ? (
            <Image source={{ uri: imagemSelecionada.uri }} style={styles.produtoImage} />
          ) : (
            <>
              <Ionicons name="cloud-upload-outline" size={40} color="#888" />
              <Text style={styles.uploadText}>Clique para adicionar imagem</Text>
            </>
          )}
          
          {/* Ícone de editar */}
          <View style={styles.editIcon}>
            <MaterialIcons name="edit" size={18} color="#555" />
          </View>
        </TouchableOpacity>

        {/* Favorito */}
        <View style={styles.favoritoContainer}>
          <Text style={styles.label}>Marcar como favorito?</Text>
          <TouchableOpacity onPress={toggleFavorito}>
            <Ionicons 
              name={isFavorito ? "heart" : "heart-outline"} 
              size={28} 
              color={isFavorito ? "#FF6B6B" : "#666"} 
            />
          </TouchableOpacity>
        </View>

        {/* Botão de Cadastro */}
        <TouchableOpacity 
          style={[styles.cadastrarButton, loading && styles.buttonDisabled]} 
          onPress={cadastrarProduto}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator color="white" />
          ) : (
            <>
              <Text style={styles.cadastrarButtonText}>Cadastrar Produto</Text>
              <Feather name="arrow-right" size={20} color="white" />
            </>
          )}
        </TouchableOpacity>
      </ScrollView>
    </LinearGradient>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    paddingTop: 40,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    width: '90%',
    marginBottom: 20,
    alignSelf: 'center',
  },
  logo: {
    width: 50,
    height: 50,
    marginRight: 10,
  },
  title: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#333',
  },
  formContainer: {
    width: '90%',
    alignSelf: 'center',
    paddingBottom: 30,
  },
  input: {
    backgroundColor: 'rgba(255,255,255,0.9)',
    borderRadius: 8,
    paddingHorizontal: 15,
    height: 45,
    fontSize: 14,
    marginBottom: 15,
    borderWidth: 1,
    borderColor: '#ccc',
    color: '#333',
  },
  label: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 5,
  },
  radioContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 15,
  },
  radioButton: {
    width: 20,
    height: 20,
    borderRadius: 10,
    borderWidth: 2,
    borderColor: '#666',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 8,
  },
  radioSelected: {
    borderColor: '#333',
  },
  radioInner: {
    width: 12,
    height: 12,
    borderRadius: 6,
    backgroundColor: '#333',
  },
  radioText: {
    fontSize: 14,
    marginRight: 20,
    color: '#333',
  },
  imageUploadContainer: {
    width: '100%',
    height: 120,
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 8,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 20,
    position: 'relative',
    backgroundColor: 'rgba(255,255,255,0.7)',
  },
  uploadText: {
    marginTop: 8,
    color: '#666',
    fontSize: 12,
  },
  editIcon: {
    position: 'absolute',
    bottom: 8,
    right: 8,
    backgroundColor: '#eee',
    padding: 5,
    borderRadius: 15,
  },
  favoritoContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 25,
    paddingHorizontal: 5,
  },
  cadastrarButton: {
    flexDirection: 'row',
    backgroundColor: '#333',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 10,
  },
  cadastrarButtonText: {
    color: 'white',
    fontWeight: 'bold',
    marginRight: 10,
    fontSize: 16,
  },
  produtoImage: {
    width: '100%',
    height: '100%',
    borderRadius: 8,
  },
  buttonDisabled: {
    opacity: 0.6,
  },
});

export default CadastroProdutoTela;