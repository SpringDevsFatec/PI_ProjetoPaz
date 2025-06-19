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
      } catch (error) {
        Alert.alert('Erro', 'Não foi possível carregar os dados do produto');
        console.error(error);
      } finally {
        setLoading(false);
      }
    };

    fetchProduto();
  }, [produtoId]);

  const toggleFavorito = () => {
    setProduto({...produto, isFavorito: !produto.isFavorito});
  };

  const editarImagem = () => {
    Alert.alert(
      'Editar Imagem',
      'Escolha uma opção:',
      [
        { text: 'Tirar Foto', onPress: () => console.log('Tirar foto') },
        { text: 'Escolher da Galeria', onPress: () => console.log('Galeria') },
        { text: 'Cancelar', style: 'cancel' },
      ]
    );
  };

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
          value={produto.nome}
          onChangeText={(text) => setProduto({...produto, nome: text})}
        />

        {/* Preço */}
        <Text style={styles.label}>Preço *</Text>
        <TextInput 
          style={styles.input} 
          value={produto.preco}
          onChangeText={(text) => setProduto({...produto, preco: text})}
          keyboardType="numeric"
        />

        {/* Categoria */}
        <Text style={styles.label}>Categoria *</Text>
        <TextInput 
          style={styles.input} 
          value={produto.categoria}
          onChangeText={(text) => setProduto({...produto, categoria: text})}
        />

        {/* Tipo */}
        <Text style={styles.label}>Tipo *</Text>
        <View style={styles.radioContainer}>
          <TouchableOpacity 
            style={[styles.radioButton, produto.tipo === 'Doação' && styles.radioSelected]}
            onPress={() => setProduto({...produto, tipo: 'Doação'})}
          >
            {produto.tipo === 'Doação' && <View style={styles.radioInner} />}
          </TouchableOpacity>
          <Text style={styles.radioText}>Doação</Text>

          <TouchableOpacity 
            style={[styles.radioButton, produto.tipo === 'Compra' && styles.radioSelected]}
            onPress={() => setProduto({...produto, tipo: 'Compra'})}
          >
            {produto.tipo === 'Compra' && <View style={styles.radioInner} />}
          </TouchableOpacity>
          <Text style={styles.radioText}>Compra</Text>
        </View>

        {/* Fornecedor */}
        <Text style={styles.label}>Fornecedor</Text>
        <TextInput 
          style={styles.input} 
          value={produto.fornecedor}
          onChangeText={(text) => setProduto({...produto, fornecedor: text})}
        />

        {/* Imagem */}
        <Text style={styles.label}>Imagem do Produto</Text>
        <View style={styles.imageUploadContainer}>
          {produto.imagemUrl ? (
            <Image source={{ uri: produto.imagemUrl }} style={styles.produtoImage} />
          ) : (
            <Ionicons name="cloud-upload-outline" size={40} color="#888" />
          )}
          <Text style={styles.uploadText}>Clique para editar imagem</Text>
          
          <TouchableOpacity style={styles.editIcon} onPress={editarImagem}>
            <MaterialIcons name="edit" size={18} color="#555" />
          </TouchableOpacity>
        </View>

        {/* Favorito */}
        <View style={styles.favoritoContainer}>
          <Text style={styles.label}>Marcar como favorito?</Text>
          <TouchableOpacity onPress={toggleFavorito}>
            <Ionicons 
              name={produto.isFavorito ? "heart" : "heart-outline"} 
              size={28} 
              color={produto.isFavorito ? "#FF6B6B" : "#666"} 
            />
          </TouchableOpacity>
        </View>

        {/* Botões */}
        <View style={styles.buttonContainer}>
          <TouchableOpacity 
            style={[styles.button, styles.cancelButton]}
            onPress={() => navigation.goBack()}
          >
            <Text style={styles.buttonText}>Cancelar</Text>
          </TouchableOpacity>
          
          <TouchableOpacity 
            style={[styles.button, styles.saveButton]}
            onPress={salvarAlteracoes}
            disabled={loading}
          >
            {loading ? (
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
  produtoImage: {
    width: '100%',
    height: '100%',
    borderRadius: 8,
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
  },
  button: {
    flex: 1,
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
    marginHorizontal: 5,
  },
  cancelButton: {
    backgroundColor: '#ccc',
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