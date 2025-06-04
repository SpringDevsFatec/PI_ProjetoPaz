import React, { useEffect } from 'react';
import { View, Text, TouchableOpacity, StyleSheet, SafeAreaView, Image, Alert, ActivityIndicator } from 'react-native';
import { Ionicons, MaterialCommunityIcons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import AsyncStorage from '@react-native-async-storage/async-storage';

const WelcomeScreen = ({ navigation }) => {
  const [loading, setLoading] = React.useState(true);

  useEffect(() => {
    const checkAuth = async () => {
      try {
        const token = await AsyncStorage.getItem('@token');
        console.log('Token encontrado:', token);
        
        if (!token) {
          console.log('Nenhum token encontrado, redirecionando para Login...');
          navigation.reset({
            index: 0,
            routes: [{ name: 'Login' }],
          });
        } else {
          setLoading(false);
        }
      } catch (error) {
        console.error('Erro ao verificar token:', error);
        Alert.alert('Erro', 'Não foi possível verificar sua autenticação');
        navigation.navigate('Login');
      }
    };

    checkAuth();
  }, [navigation]);

  const handleSistemaPress = () => {
    navigation.navigate('GestaoProdutos');
  };

  if (loading) {
    return (
      <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={[styles.container, { justifyContent: 'center' }]}>
        <ActivityIndicator size="large" color="#333" />
      </LinearGradient>
    );
  }

  return (
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.container}>
      <SafeAreaView style={styles.safeArea}>
        <View style={styles.content}>
          <Image source={require('../../assets/images/logopaz.png')} style={styles.logo} />
          
          <View style={styles.card}>
            <Text style={styles.titulo}>Seja bem-vindo!</Text>
            <Text style={styles.subtitulo}>
              Nosso aplicativo visa facilitar e organizar suas vendas na igreja Santo Agostinho.
            </Text>

            <View style={styles.opcoesContainer}>
              <TouchableOpacity 
                style={styles.opcao}
                onPress={handleSistemaPress}
                activeOpacity={0.7}
              >
                <Ionicons name="person-circle-outline" size={55} color="white" />
                <Text style={styles.textoOpcao}>Sistema</Text>
              </TouchableOpacity>

              <TouchableOpacity 
                style={styles.opcao}
                onPress={() => navigation.navigate('Products')} 
              >
                <MaterialCommunityIcons name="cart-outline" size={55} color="white" />
                <Text style={styles.textoOpcao}>Comece a vender</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </SafeAreaView>
    </LinearGradient>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  safeArea: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  content: {
    width: '85%',
    alignItems: 'center',
  },
  logo: {
    width: 180,
    height: 180,
    marginBottom: 25,
  },
  card: {
    backgroundColor: '#333',
    padding: 20,
    borderRadius: 10,
    width: '100%',
    alignItems: 'center',
  },
  titulo: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#fff',
    textAlign: 'center',
    marginBottom: 5,
  },
  subtitulo: {
    fontSize: 14,
    color: '#ccc',
    textAlign: 'center',
    marginBottom: 20,
  },
  opcoesContainer: {
    flexDirection: 'row',
    width: '100%',
    justifyContent: 'space-between',
  },
  opcao: {
    alignItems: 'center',
    flex: 1,
  },
  textoOpcao: {
    color: 'white',
    marginTop: 5,
    fontSize: 14,
  },
});

export default WelcomeScreen;
