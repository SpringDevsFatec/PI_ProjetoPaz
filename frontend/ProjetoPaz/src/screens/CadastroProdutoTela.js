import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Image, Alert, ScrollView } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons, MaterialIcons, Feather } from '@expo/vector-icons';

const CadastroProdutoTela = ({ navigation }) => {
  // Estados do formulário
  const [nome, setNome] = useState('');
  const [preco, setPreco] = useState('');
  const [categoria, setCategoria] = useState('');
  const [tipo, setTipo] = useState('');
  const [fornecedor, setFornecedor] = useState('');
  const [isFavorito, setIsFavorito] = useState(false);

  // Função para alternar favorito
  const toggleFavorito = () => {
    setIsFavorito(!isFavorito);
  };

  // Função para editar imagem
  const editarImagem = () => {
    Alert.alert(
      'Editar Imagem',
      'Escolha uma opção:',
      [
        {
          text: 'Tirar Foto',
          onPress: () => console.log('Tirar foto selecionado'),
        },
        {
          text: 'Escolher da Galeria',
          onPress: () => console.log('Galeria selecionada'),
        },
        {
          text: 'Cancelar',
          style: 'cancel',
        },
      ],
      { cancelable: true }
    );
  };

  // Função para cadastrar produto
  const cadastrarProduto = () => {
    if (!nome || !preco || !categoria || !tipo) {
      Alert.alert('Atenção', 'Preencha todos os campos obrigatórios');
      return;
    }

    const produto = {
      nome,
      preco,
      categoria,
      tipo,
      fornecedor,
      isFavorito,
      dataCadastro: new Date().toLocaleDateString(),
    };

    console.log('Produto cadastrado:', produto);
    Alert.alert('Sucesso', 'Produto cadastrado com sucesso!');
    navigation.goBack();
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
        <View style={styles.imageUploadContainer}>
          <Ionicons name="cloud-upload-outline" size={40} color="#888" />
          <Text style={styles.uploadText}>Clique para adicionar imagem</Text>
          
          {/* Ícone de editar */}
          <TouchableOpacity style={styles.editIcon} onPress={editarImagem}>
            <MaterialIcons name="edit" size={18} color="#555" />
          </TouchableOpacity>
        </View>

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
        <TouchableOpacity style={styles.cadastrarButton} onPress={cadastrarProduto}>
          <Text style={styles.cadastrarButtonText}>Cadastrar Produto</Text>
          <Feather name="arrow-right" size={20} color="white" />
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
});

export default CadastroProdutoTela;