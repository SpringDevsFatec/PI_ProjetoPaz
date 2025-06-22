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
    category: '',
    donation: '0',
    namesupplier: '',
    is_favorite: '0',
    status: '1',
    image: null,
    imageBase64: ''
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
          category: data.category || '',
          donation: data.is_donation?.toString() || '0',
          namesupplier: data.supplier?.name || '',
          is_favorite: data.is_favorite?.toString() || '0',
          status: data.status?.toString() || '1',
          image: data.img_product || null,
          imageBase64: data.img_product || ''
        });

      } catch (error) {
        Alert.alert('Erro', 'Não foi possível carregar os dados do produto');
        console.error(error);
      } finally {
        setLoading(false);
      }
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
  };

  const editarImagem = () => {
    Alert.alert(
      'Editar Imagem',
      'Escolha uma opção:',
      [
        { text: 'Tirar Foto', onPress: takePhoto },
        { text: 'Escolher da Galeria', onPress: pickImage },
        {
          text: 'Remover Imagem',
          onPress: () => setProduto({...produto, image: null, imageBase64: ''}),
          style: 'destructive'
        },
        { text: 'Cancelar', style: 'cancel' },
      ]
    );
  };

  // Função para salvar alterações
  const salvarAlteracoes = async () => {
    if (!produto.nameproduct || !produto.sale_price || !produto.category) {
      Alert.alert('Atenção', 'Preencha todos os campos obrigatórios');
      return;
    }

    try {
      setUploading(true);
      
      // Primeiro atualiza os dados básicos do produto
      const produtoData = {
        nameproduct: produto.nameproduct,
        sale_price: produto.sale_price,
        category: produto.category,
        donation: produto.donation,
        namesupplier: produto.namesupplier,
        is_favorite: produto.is_favorite,
        status: produto.status
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
          value={produto.nameproduct}
          onChangeText={(text) => setProduto({...produto, nameproduct: text})}
        />

        {/* Preço */}
        <Text style={styles.label}>Preço *</Text>
        <TextInput 
          style={styles.input} 
          value={produto.sale_price}
          onChangeText={(text) => setProduto({...produto, sale_price: text})}
          keyboardType="numeric"
        />

        {/* Categoria */}
        <Text style={styles.label}>Categoria *</Text>
        <TextInput 
          style={styles.input} 
          value={produto.category}
          onChangeText={(text) => setProduto({...produto, category: text})}
        />

        {/* Tipo */}
        <Text style={styles.label}>Tipo *</Text>
        <View style={styles.radioContainer}>
          <TouchableOpacity 
            style={[styles.radioButton, produto.donation === '1' && styles.radioSelected]}
            onPress={() => setProduto({...produto, donation: '1'})}
          >
            {produto.donation === '1' && <View style={styles.radioInner} />}
          </TouchableOpacity>
          <Text style={styles.radioText}>Doação</Text>

          <TouchableOpacity 
            style={[styles.radioButton, produto.donation === '0' && styles.radioSelected]}
            onPress={() => setProduto({...produto, donation: '0'})}
          >
            {produto.donation === '0' && <View style={styles.radioInner} />}
          </TouchableOpacity>
          <Text style={styles.radioText}>Compra</Text>
        </View>

        {/* Fornecedor */}
        <Text style={styles.label}>Fornecedor</Text>
        <TextInput 
          style={styles.input} 
          value={produto.namesupplier}
          onChangeText={(text) => setProduto({...produto, namesupplier: text})}
        />

        {/* Imagem */}
        <Text style={styles.label}>Imagem do Produto</Text>
        <View style={styles.imageUploadContainer}>
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
          </TouchableOpacity>
        </View>

        {/* Favorito */}
        <View style={styles.favoritoContainer}>
          <Text style={styles.label}>Marcar como favorito?</Text>
          <TouchableOpacity onPress={() => setProduto({...produto, is_favorite: produto.is_favorite === '1' ? '0' : '1'})}>
            <Ionicons 
              name={produto.is_favorite === '1' ? "heart" : "heart-outline"} 
              size={28} 
              color={produto.is_favorite === '1' ? "#FF6B6B" : "#666"} 
            />
          </TouchableOpacity>
        </View>

        {/* Botões */}
        <View style={styles.buttonContainer}>
          <TouchableOpacity 
            style={[styles.button, styles.inactivateButton]}
            onPress={inativarProduto}
            disabled={uploading}
          >
            <Text style={styles.buttonText}>Inativar Produto</Text>
          </TouchableOpacity>
          
          <TouchableOpacity 
            style={[styles.button, styles.saveButton]}
            onPress={salvarAlteracoes}
            disabled={uploading}
          >
            {uploading ? (
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

// Estilos (mantidos os mesmos do seu código original)
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
    marginTop: 10,
  },
  button: {
    flex: 1,
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
    marginHorizontal: 5,
  },
  inactivateButton: {
    backgroundColor: '#e74c3c',
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