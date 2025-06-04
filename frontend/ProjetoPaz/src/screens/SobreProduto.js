import React from 'react';
import { View, Text, TouchableOpacity, Image, StyleSheet } from 'react-native';
import Ionicons from '@expo/vector-icons/Ionicons';

const SobreProduto = ({ navigation }) => {
  return (
    <View style={styles.container}>
     
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

      <Text style={styles.titulo}>Sobre o Produto</Text>

      <View style={styles.card}>
        <View style={styles.imagemBox}></View>
        <Text style={styles.precoTexto}>Preço:</Text>
        <Text style={styles.categoriaTexto}>Categoria:</Text>
        <TouchableOpacity style={styles.editarBtn}>
          <Ionicons name="pencil-outline" size={20} color="black" />
        </TouchableOpacity>
      </View>

    
      <View style={styles.footer}>
        <TouchableOpacity onPress={() => navigation.navigate('Home')}>
          <Ionicons name="home-outline" size={26} color="white" />
        </TouchableOpacity>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: { 
    flex: 1, 
    backgroundColor: '#fff' 
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 40,
    paddingHorizontal: 20,  
  },
  logo: { width: 60, height: 60, resizeMode: 'contain' },
  titulo: { 
    textAlign: 'center', 
    fontSize: 20, 
    fontWeight: 'bold', 
    marginVertical: 20 
  },
  card: {
    borderWidth: 1,
    borderColor: '#333',
    borderRadius: 10,
    padding: 20,
    alignItems: 'center',
    marginHorizontal: 20,  
  },
  imagemBox: {
    width: 100,
    height: 100,
    backgroundColor: '#f0f0f0',
    marginBottom: 20,
    borderRadius: 8,
  },
  precoTexto: { fontSize: 16, fontWeight: 'bold', marginBottom: 8 },
  categoriaTexto: { fontSize: 16, fontWeight: 'bold' },
  editarBtn: { alignSelf: 'flex-end', marginTop: 15 },
  footer: {
    backgroundColor: '#333',
    paddingVertical: 15,
    alignItems: 'center',
    justifyContent: 'center',
    width: '100%',
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
  },
});


export default SobreProduto;
