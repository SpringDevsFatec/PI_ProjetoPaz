import React, { useState, useEffect } from 'react';
import { 
  View, 
  Text, 
  TextInput, 
  TouchableOpacity, 
  StyleSheet, 
  Image, 
  Alert,
  ScrollView,
<<<<<<< HEAD
  ActivityIndicator
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons, MaterialIcons, Feather } from '@expo/vector-icons';

const EditarProdutoTela = ({ route, navigation }) => {
  const { produtoId } = route.params; // Recebe o ID do produto a ser editado
  const [loading, setLoading] = useState(true);
  const [produto, setProduto] = useState({
    nome: '',
    preco: '',
    categoria: '',
    tipo: '',
    fornecedor: '',
    isFavorito: false,
    imagemUrl: null
  });

  // Busca os dados do produto ao carregar a tela
  useEffect(() => {
    const fetchProduto = async () => {
      try {
        setLoading(true);
        // Substitua pela sua chamada real à API
        const response = await axios.get(`/produtos/${produtoId}`);
        setProduto(response.data);
=======
  ActivityIndicator,
  Platform
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons, MaterialIcons, Feather } from '@expo/vector-icons';
import * as ImagePicker from 'expo-image-picker';
import { manipulateAsync, SaveFormat } from 'expo-image-manipulator';
import api from '../services/api'; // Importe sua instância do axios

const EditarProdutoTela = ({ route, navigation }) => {
  const { produtoId } = route.params;
  const [loading, setLoading] = useState(true);
  const [uploading, setUploading] = useState(false);
  const [produto, setProduto] = useState({
    nameproduct: '',
    sale_price: '',
    cost_price: '',
    category: '',
    donation: '0',
    namesupplier: '',
    location: '',
    description: '',
    is_favorite: '0',
    status: '1',
    image: null,
    imageBase64: '',
    idSupplier: ''
  });

  // Solicitar permissões e carregar produto
  useEffect(() => {
    (async () => {
      // Solicitar permissões
      if (Platform.OS !== 'web') {
        await ImagePicker.requestMediaLibraryPermissionsAsync();
        await ImagePicker.requestCameraPermissionsAsync();
      }

      // Carregar dados do produto
      try {
        setLoading(true);
        const response = await api.get(`/products/${produtoId}`);
        const data = response.data?.content || response.data;
        
        setProduto({
          nameproduct: data.name || '',
          sale_price: data.sale_price?.toString() || '',
          cost_price: data.cost_price?.toString() || '',
          category: data.category || '',
          donation: data.is_donation?.toString() || '0',
          namesupplier: data.supplier?.name || '',
          location: data.supplier?.location || '',
          description: data.description || '',
          is_favorite: data.is_favorite?.toString() || '0',
          status: data.status?.toString() || '1',
          image: data.img_product || null,
          imageBase64: data.img_product || '',
          idSupplier: data.supplier?.id || ''
        });

        console.log("Dados com produto criado:   " , produto);

>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
      } catch (error) {
        Alert.alert('Erro', 'Não foi possível carregar os dados do produto');
        console.error(error);
      } finally {
        setLoading(false);
      }
<<<<<<< HEAD
    };

    fetchProduto();
  }, [produtoId]);

  const toggleFavorito = () => {
    setProduto({...produto, isFavorito: !produto.isFavorito});
=======
    })();
  }, [produtoId]);

  // Funções para manipulação de imagem
  const pickImage = async () => {
    try {
      let result = await ImagePicker.launchImageLibraryAsync({
        mediaTypes: ImagePicker.MediaTypeOptions.Images,
        allowsEditing: true,
        aspect: [4, 3],
        quality: 0.7,
        base64: true,
      });

      if (!result.canceled) {
        const compressedImage = await manipulateAsync(
          result.assets[0].uri,
          [{ resize: { width: 800 } }],
          { compress: 0.7, format: SaveFormat.JPEG, base64: true }
        );
        
        setProduto({
          ...produto,
          image: compressedImage.uri,
          imageBase64: `data:image/jpeg;base64,${compressedImage.base64}`
        });
      }
    } catch (error) {
      console.error('Erro ao selecionar imagem:', error);
      Alert.alert('Erro', 'Não foi possível selecionar a imagem');
    }
  };

  const takePhoto = async () => {
    try {
      let result = await ImagePicker.launchCameraAsync({
        allowsEditing: true,
        aspect: [4, 3],
        quality: 0.7,
        base64: true,
      });

      if (!result.canceled) {
        const compressedImage = await manipulateAsync(
          result.assets[0].uri,
          [{ resize: { width: 800 } }],
          { compress: 0.7, format: SaveFormat.JPEG, base64: true }
        );
        
        setProduto({
          ...produto,
          image: compressedImage.uri,
          imageBase64: `data:image/jpeg;base64,${compressedImage.base64}`
        });
      }
    } catch (error) {
      console.error('Erro ao tirar foto:', error);
      Alert.alert('Erro', 'Não foi possível tirar a foto');
    }
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
  };

  const editarImagem = () => {
    Alert.alert(
      'Editar Imagem',
      'Escolha uma opção:',
      [
<<<<<<< HEAD
        { text: 'Tirar Foto', onPress: () => console.log('Tirar foto') },
        { text: 'Escolher da Galeria', onPress: () => console.log('Galeria') },
=======
        { text: 'Tirar Foto', onPress: takePhoto },
        { text: 'Escolher da Galeria', onPress: pickImage },
        {
          text: 'Remover Imagem',
          onPress: () => setProduto({...produto, image: null, imageBase64: ''}),
          style: 'destructive'
        },
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
        { text: 'Cancelar', style: 'cancel' },
      ]
    );
  };

<<<<<<< HEAD
  const salvarAlteracoes = async () => {
    try {
      setLoading(true);
      // Chamada para atualizar no backend
      await axios.put(`/produtos/${produtoId}`, produto);
      Alert.alert('Sucesso', 'Produto atualizado com sucesso!');
      navigation.goBack();
    } catch (error) {
      Alert.alert('Erro', 'Falha ao atualizar produto');
      console.error(error);
    } finally {
      setLoading(false);
=======
  // Função para salvar alterações
  const salvarAlteracoes = async () => {
    if (!produto.nameproduct || !produto.sale_price || !produto.cost_price || !produto.category) {
      Alert.alert('Atenção', 'Preencha todos os campos obrigatórios');
      return;
    }

    try {
      setUploading(true);
      
      // Primeiro atualiza os dados básicos do produto
      const produtoData = {
        nameproduct: produto.nameproduct,
        sale_price: produto.sale_price,
        cost_price: produto.cost_price,
        category: produto.category,
        description: produto.description,
        donation: produto.donation,
        namesupplier: produto.namesupplier,
        location: produto.location,
        is_favorite: produto.is_favorite,
        status: produto.status,
        idSupplier: produto.idSupplier
      };

      await api.put(`/products/${produtoId}`, produtoData);

      // Se há uma nova imagem, faz upload separado
      if (produto.imageBase64 && !produto.imageBase64.startsWith('http')) {
        await api.put(`/products/img/${produtoId}`, {
          image: produto.imageBase64
        });
      }

      Alert.alert('Sucesso', 'Produto atualizado com sucesso!');
      navigation.goBack();
    } catch (error) {
      console.error('Erro ao atualizar produto:', error);
      Alert.alert('Erro', error.response?.data?.message || 'Falha ao atualizar produto');
    } finally {
      setUploading(false);
    }
  };

  // Função para inativar produto
  const inativarProduto = async () => {
    try {
      setUploading(true);
      await api.put(`/products/inactivate/${produtoId}`);
      Alert.alert('Sucesso', 'Produto inativado com sucesso!');
      navigation.goBack();
    } catch (error) {
      console.error('Erro ao inativar produto:', error);
      Alert.alert('Erro', 'Falha ao inativar produto');
    } finally {
      setUploading(false);
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
    }
  };

  if (loading) {
    return (
      <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={[styles.container, styles.loadingContainer]}>
        <ActivityIndicator size="large" color="#333" />
      </LinearGradient>
    );
  }

  return (
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.container}>
      {/* Cabeçalho */}
      <View style={styles.header}>
        <Image source={require('../../assets/images/logopaz.png')} style={styles.logo} />
        <Text style={styles.title}>Editar Produto</Text>
      </View>

      {/* Formulário */}
      <ScrollView contentContainerStyle={styles.formContainer}>
        {/* Nome */}
        <Text style={styles.label}>Nome do Produto *</Text>
        <TextInput 
          style={styles.input} 
<<<<<<< HEAD
          value={produto.nome}
          onChangeText={(text) => setProduto({...produto, nome: text})}
=======
          value={produto.nameproduct}
          onChangeText={(text) => setProduto({...produto, nameproduct: text})}
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
        />

        {/* Preço */}
        <Text style={styles.label}>Preço *</Text>
        <TextInput 
          style={styles.input} 
<<<<<<< HEAD
          value={produto.preco}
          onChangeText={(text) => setProduto({...produto, preco: text})}
=======
          value={produto.sale_price}
          onChangeText={(text) => setProduto({...produto, sale_price: text})}
          keyboardType="numeric"
        />

        {/* Preço de Custo */}
        <Text style={styles.label}>Preço de Custo *</Text>
        <TextInput 
          style={styles.input} 
          value={produto.cost_price}
          onChangeText={(text) => setProduto({...produto, cost_price: text})}
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
          keyboardType="numeric"
        />

        {/* Categoria */}
        <Text style={styles.label}>Categoria *</Text>
        <TextInput 
          style={styles.input} 
<<<<<<< HEAD
          value={produto.categoria}
          onChangeText={(text) => setProduto({...produto, categoria: text})}
=======
          value={produto.category}
          onChangeText={(text) => setProduto({...produto, category: text})}
        />

        <Text style={styles.label}>Descrição</Text>
        <TextInput 
          style={[styles.input, {height: 80}]} 
          value={produto.description}
          onChangeText={(text) => setProduto({...produto, description: text})}
          multiline
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
        />

        {/* Tipo */}
        <Text style={styles.label}>Tipo *</Text>
        <View style={styles.radioContainer}>
          <TouchableOpacity 
<<<<<<< HEAD
            style={[styles.radioButton, produto.tipo === 'Doação' && styles.radioSelected]}
            onPress={() => setProduto({...produto, tipo: 'Doação'})}
          >
            {produto.tipo === 'Doação' && <View style={styles.radioInner} />}
=======
            style={[styles.radioButton, produto.donation === '1' && styles.radioSelected]}
            onPress={() => setProduto({...produto, donation: '1'})}
          >
            {produto.donation === '1' && <View style={styles.radioInner} />}
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
          </TouchableOpacity>
          <Text style={styles.radioText}>Doação</Text>

          <TouchableOpacity 
<<<<<<< HEAD
            style={[styles.radioButton, produto.tipo === 'Compra' && styles.radioSelected]}
            onPress={() => setProduto({...produto, tipo: 'Compra'})}
          >
            {produto.tipo === 'Compra' && <View style={styles.radioInner} />}
=======
            style={[styles.radioButton, produto.donation === '0' && styles.radioSelected]}
            onPress={() => setProduto({...produto, donation: '0'})}
          >
            {produto.donation === '0' && <View style={styles.radioInner} />}
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
          </TouchableOpacity>
          <Text style={styles.radioText}>Compra</Text>
        </View>

        {/* Fornecedor */}
        <Text style={styles.label}>Fornecedor</Text>
        <TextInput 
          style={styles.input} 
<<<<<<< HEAD
          value={produto.fornecedor}
          onChangeText={(text) => setProduto({...produto, fornecedor: text})}
=======
          value={produto.namesupplier}
          onChangeText={(text) => setProduto({...produto, namesupplier: text})}
        />

        {/* Localização */}
        <Text style={styles.label}>Localização *</Text>
        <TextInput 
          style={styles.input} 
          value={produto.location}
          onChangeText={(text) => setProduto({...produto, location: text})}
          placeholder="Ex: Paróquia de St Terezinha"
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
        />

        {/* Imagem */}
        <Text style={styles.label}>Imagem do Produto</Text>
        <View style={styles.imageUploadContainer}>
<<<<<<< HEAD
          {produto.imagemUrl ? (
            <Image source={{ uri: produto.imagemUrl }} style={styles.produtoImage} />
          ) : (
            <Ionicons name="cloud-upload-outline" size={40} color="#888" />
          )}
          <Text style={styles.uploadText}>Clique para editar imagem</Text>
          
          <TouchableOpacity style={styles.editIcon} onPress={editarImagem}>
            <MaterialIcons name="edit" size={18} color="#555" />
=======
          {produto.image ? (
            <Image 
              source={{ uri: produto.image.startsWith('data:') ? produto.image : produto.image }} 
              style={{ width: '100%', height: '100%', borderRadius: 8 }} 
              resizeMode="cover"
            />
          ) : (
            <>
              <Ionicons name="cloud-upload-outline" size={40} color="#888" />
              <Text style={styles.uploadText}>Clique para adicionar imagem</Text>
            </>
          )}
          
          <TouchableOpacity style={styles.editIcon} onPress={editarImagem}>
            <MaterialIcons name={produto.image ? "edit" : "add-a-photo"} size={18} color="#555" />
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
          </TouchableOpacity>
        </View>

        {/* Favorito */}
        <View style={styles.favoritoContainer}>
          <Text style={styles.label}>Marcar como favorito?</Text>
<<<<<<< HEAD
          <TouchableOpacity onPress={toggleFavorito}>
            <Ionicons 
              name={produto.isFavorito ? "heart" : "heart-outline"} 
              size={28} 
              color={produto.isFavorito ? "#FF6B6B" : "#666"} 
=======
          <TouchableOpacity onPress={() => setProduto({...produto, is_favorite: produto.is_favorite === '1' ? '0' : '1'})}>
            <Ionicons 
              name={produto.is_favorite === '1' ? "heart" : "heart-outline"} 
              size={28} 
              color={produto.is_favorite === '1' ? "#FF6B6B" : "#666"} 
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
            />
          </TouchableOpacity>
        </View>

        {/* Botões */}
        <View style={styles.buttonContainer}>
          <TouchableOpacity 
<<<<<<< HEAD
            style={[styles.button, styles.cancelButton]}
            onPress={() => navigation.goBack()}
          >
            <Text style={styles.buttonText}>Cancelar</Text>
=======
            style={[styles.button, styles.inactivateButton]}
            onPress={inativarProduto}
            disabled={uploading}
          >
            <Text style={styles.buttonText}>Inativar Produto</Text>
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
          </TouchableOpacity>
          
          <TouchableOpacity 
            style={[styles.button, styles.saveButton]}
            onPress={salvarAlteracoes}
<<<<<<< HEAD
            disabled={loading}
          >
            {loading ? (
=======
            disabled={uploading}
          >
            {uploading ? (
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
              <ActivityIndicator color="white" />
            ) : (
              <Text style={styles.buttonText}>Salvar Alterações</Text>
            )}
          </TouchableOpacity>
        </View>
      </ScrollView>
    </LinearGradient>
  );
};

<<<<<<< HEAD
=======
// Estilos (mantidos os mesmos do seu código original)
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
const styles = StyleSheet.create({
  container: {
    flex: 1,
    paddingTop: 40,
  },
  loadingContainer: {
    justifyContent: 'center',
    alignItems: 'center',
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
<<<<<<< HEAD
  produtoImage: {
    width: '100%',
    height: '100%',
    borderRadius: 8,
  },
=======
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
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
  buttonContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
<<<<<<< HEAD
=======
    marginTop: 10,
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
  },
  button: {
    flex: 1,
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
    marginHorizontal: 5,
  },
<<<<<<< HEAD
  cancelButton: {
    backgroundColor: '#ccc',
=======
  inactivateButton: {
    backgroundColor: '#e74c3c',
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
  },
  saveButton: {
    backgroundColor: '#333',
  },
  buttonText: {
    color: 'white',
    fontWeight: 'bold',
    fontSize: 16,
  },
});

export default EditarProdutoTela;