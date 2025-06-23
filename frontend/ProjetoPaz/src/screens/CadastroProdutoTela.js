import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Image, Alert, ScrollView, Platform } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons, MaterialIcons, Feather } from '@expo/vector-icons';
import * as ImagePicker from 'expo-image-picker';
import api from '../services/api';

const CadastroProdutoTela = ({ navigation }) => {
  // Estados do formulário
  const [nome, setNome] = useState('');
  const [preco, setPreco] = useState('');
  const [categoria, setCategoria] = useState('');
  const [tipo, setTipo] = useState('');
  const [fornecedor, setFornecedor] = useState('');
  const [isFavorito, setIsFavorito] = useState(false);
  const [imagem, setImagem] = useState(null);
  const [imagemBase64, setImagemBase64] = useState('');
  const [precoCusto, setPrecoCusto] = useState('');
  const [descricao, setDescricao] = useState('');
  const [localizacao, setLocalizacao] = useState('');
    const handleToggleFavorito = () => {
    setIsFavorito(!isFavorito);
  };

  // Solicitar permissão para acessar a galeria/câmera
  useEffect(() => {
    (async () => {
      if (Platform.OS !== 'web') {
        const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
        if (status !== 'granted') {
          Alert.alert('Permissão necessária', 'Precisamos da permissão para acessar suas fotos!');
        }
      }
    })();
  }, []);

  // Função para selecionar imagem da galeria
const pickImage = async () => {
  try {
    let result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.All, // ← Corrigido aqui
      allowsEditing: true,
      aspect: [4, 3],
      quality: 0.5,
      base64: true,
    });

    if (!result.canceled) { // ← 'cancelled' mudou para 'canceled' em versões mais novas
      setImagem(result.assets[0].uri);
      setImagemBase64(`data:image/jpeg;base64,${result.assets[0].base64}`);
    }
  } catch (error) {
    console.error('Erro ao selecionar imagem:', error);
    Alert.alert('Erro', 'Não foi possível selecionar a imagem');
  }
};

// Função para tirar foto (atualizada)
const takePhoto = async () => {
  try {
    let result = await ImagePicker.launchCameraAsync({
      mediaTypes: ImagePicker.MediaType.Images, // ← Corrigido aqui
      allowsEditing: true,
      aspect: [4, 3],
      quality: 0.5,
      base64: true,
    });

    if (!result.canceled) { // ← Atualizado aqui
      setImagem(result.assets[0].uri);
      setImagemBase64(`data:image/jpeg;base64,${result.assets[0].base64}`);
    }
  } catch (error) {
    console.error('Erro ao tirar foto:', error);
    Alert.alert('Erro', 'Não foi possível tirar a foto');
  }
};

  // Função para editar imagem
  const editarImagem = () => {
    Alert.alert(
      'Editar Imagem',
      'Escolha uma opção:',
      [
        {
          text: 'Tirar Foto',
          onPress: takePhoto,
        },
        {
          text: 'Escolher da Galeria',
          onPress: pickImage,
        },
        {
          text: 'Remover Imagem',
          onPress: () => {
            setImagem(null);
            setImagemBase64('');
          },
          style: 'destructive',
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
  const cadastrarProduto = async () => {
    if (!nome || !preco || !categoria || !tipo) {
      Alert.alert('Atenção', 'Preencha todos os campos obrigatórios');
      return;
    }

    const produto = {
      nameproduct: nome,
      cost_price: precoCusto || '0', // Adicionado
      sale_price: preco,
      description: descricao || 'Produto cadastrado via app', // Atualizado
      is_favorite: isFavorito ? '1' : '0',
      category: categoria,
      donation: tipo === 'Doação' ? '1' : '0',
      status: '1',
      namesupplier: fornecedor || 'Não especificado', // Atualizado
      location: localizacao || 'Local não especificado', // Adicionado
      image: imagemBase64,
    };

    try {
      const response = await api.post('/product', produto);
      
      if (response.status === 200 || response.status === 201) {
        Alert.alert('Sucesso', 'Produto cadastrado com sucesso!');
        navigation.goBack();
      } else {
        Alert.alert('Erro', 'Ocorreu um erro ao cadastrar o produto');
      }
    } catch (error) {
      console.error('Erro ao cadastrar produto:', error);
      Alert.alert('Erro', error.response?.data?.message || 'Falha ao conectar com o servidor');
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

        {/* Preço de Custo */}
        <Text style={styles.label}>Preço de Custo</Text>
        <TextInput 
          style={styles.input} 
          placeholder="R$ 0,00" 
          placeholderTextColor="#666"
          keyboardType="numeric"
          value={precoCusto}
          onChangeText={setPrecoCusto}
        />

        {/* Descrição */}
        <Text style={styles.label}>Descrição</Text>
        <TextInput 
          style={[styles.input, { height: 80, textAlignVertical: 'top' }]} 
          placeholder="Descrição do produto" 
          placeholderTextColor="#666"
          multiline
          value={descricao}
          onChangeText={setDescricao}
        />

        {/* Localização */}
        <Text style={styles.label}>Localização</Text>
        <TextInput 
          style={styles.input} 
          placeholder="Onde o produto está disponível" 
          placeholderTextColor="#666"
          value={localizacao}
          onChangeText={setLocalizacao}
/>

      {/* Imagem */}
      <Text style={styles.label}>Imagem do Produto</Text>
      <View style={styles.imageUploadContainer}>
        {imagem ? (
          <Image 
            source={{ uri: imagem }} 
            style={{ width: '100%', height: '100%', borderRadius: 8 }} 
            resizeMode="cover"
          />
        ) : (
          <>
            <Ionicons name="cloud-upload-outline" size={40} color="#888" />
            <Text style={styles.uploadText}>Clique para adicionar imagem</Text>
          </>
        )}
        
        {/* Ícone de editar */}
        <TouchableOpacity style={styles.editIcon} onPress={editarImagem}>
          <MaterialIcons name={imagem ? "edit" : "add-a-photo"} size={18} color="#555" />
        </TouchableOpacity>
      </View>


      {/* Favorito */}
      <View style={styles.favoritoContainer}>
        <Text style={styles.label}>Marcar como favorito?</Text>
        <TouchableOpacity onPress={handleToggleFavorito}>
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
  descricaoInput: {
  backgroundColor: 'rgba(255,255,255,0.9)',
  borderRadius: 8,
  paddingHorizontal: 15,
  height: 80,
  fontSize: 14,
  marginBottom: 15,
  borderWidth: 1,
  borderColor: '#ccc',
  color: '#333',
  textAlignVertical: 'top',
},
});

export default CadastroProdutoTela;