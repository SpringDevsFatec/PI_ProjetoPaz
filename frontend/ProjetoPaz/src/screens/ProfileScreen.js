import React, { useContext, useState, useEffect } from 'react';
import {
  View,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  Text,
  Alert,
  ScrollView,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { AuthContext } from '../contexts/AuthContext';
import api from '../services/api';
import { useNavigation } from '@react-navigation/native';

const ProfileScreen = () => {
  const { user, setUser, logout } = useContext(AuthContext);
  const navigation = useNavigation();
  const [isEditing, setIsEditing] = useState(false);

  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');

  useEffect(() => {
    if (user) {
      setName(user.name || '');
      setEmail(user.email || '');
      setPassword('');
      setConfirmPassword('');
    }
  }, [user]);

  const saveProfile = async () => {
    if (password && password !== confirmPassword) {
      Alert.alert('Erro', 'As senhas não coincidem.');
      return;
    }

    try {
      const body = {
        name,
        email,
      };

      if (password) {
        body.password = password;
      }

      const response = await api.put('/users', body);

      if (response.data.status) {
        setUser(response.data.content);
        setIsEditing(false);
        setPassword('');
        setConfirmPassword('');
        Alert.alert('Sucesso', 'Perfil atualizado com sucesso!');
      } else {
        Alert.alert('Erro', response.data.message || 'Erro ao atualizar perfil');
      }
    } catch (error) {
      console.error('Erro ao atualizar perfil:', error);
      Alert.alert('Erro', 'Erro ao atualizar perfil');
    }
  };

  const handleLogout = () => {
    Alert.alert('Sair', 'Tem certeza que deseja sair?', [
      { text: 'Cancelar', style: 'cancel' },
      {
        text: 'Sair',
        style: 'destructive',
        onPress: async () => {
          await logout();
          navigation.replace('Login');
        },
      },
    ]);
  };

  return (
    <LinearGradient
      colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']}
      style={styles.container}
    >
      <ScrollView contentContainerStyle={styles.scrollContainer}>
        <View style={styles.header}>
          <Text style={styles.title}>Perfil</Text>
        </View>

        <View style={styles.avatarContainer}>
          <Ionicons name="person-circle-outline" size={100} color="#aaa" />
        </View>

        <View style={styles.infoContainer}>
          <Text style={styles.label}>Nome</Text>
          <TextInput
            style={styles.input}
            value={name}
            onChangeText={setName}
            editable={isEditing}
          />

          <Text style={styles.label}>Email</Text>
          <TextInput
            style={styles.input}
            value={email}
            onChangeText={setEmail}
            editable={isEditing}
            keyboardType="email-address"
            autoCapitalize="none"
          />

          {isEditing && (
            <>
              <Text style={styles.label}>Nova Senha</Text>
              <TextInput
                style={styles.input}
                value={password}
                onChangeText={setPassword}
                editable={isEditing}
                secureTextEntry
                placeholder="Digite a nova senha"
              />

              <Text style={styles.label}>Confirmar Senha</Text>
              <TextInput
                style={styles.input}
                value={confirmPassword}
                onChangeText={setConfirmPassword}
                editable={isEditing}
                secureTextEntry
                placeholder="Confirme a nova senha"
              />
            </>
          )}
        </View>

        <TouchableOpacity
          onPress={() => {
            if (isEditing) {
              saveProfile();
            } else {
              setIsEditing(true);
            }
          }}
          style={styles.button}
        >
          <Text style={styles.buttonText}>
            {isEditing ? 'Salvar' : 'Editar Perfil'}
          </Text>
        </TouchableOpacity>

        <TouchableOpacity onPress={handleLogout} style={styles.logoutButton}>
          <Text style={styles.logoutButtonText}>Sair</Text>
        </TouchableOpacity>

        <View style={{ height: 80 }} />
      </ScrollView>

      <View style={styles.footer}>
        <Ionicons name="home-outline" size={24} color="white" />
        <Ionicons name="person" size={24} color="white" />
      </View>
    </LinearGradient>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1 },
  scrollContainer: {
    paddingTop: 50,
    alignItems: 'center',
    paddingBottom: 100,
  },
  header: { width: '90%', marginBottom: 20 },
  title: { fontSize: 22, fontWeight: 'bold' },
  avatarContainer: { marginBottom: 30 },
  infoContainer: { width: '90%' },
  label: { fontSize: 14, marginBottom: 4, marginTop: 10 },
  input: { backgroundColor: '#f0f0f0', padding: 10, borderRadius: 8 },
  button: {
    backgroundColor: '#333',
    width: '90%',
    padding: 12,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 25,
  },
  buttonText: { color: '#fff', fontWeight: 'bold' },
  logoutButton: {
    backgroundColor: '#c00',
    width: '90%',
    padding: 12,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 10,
  },
  logoutButtonText: {
    color: '#fff',
    fontWeight: 'bold',
  },
  footer: {
    position: 'absolute',
    bottom: 0,
    width: '100%',
    backgroundColor: '#333',
    flexDirection: 'row',
    justifyContent: 'space-around',
    paddingVertical: 10,
  },
});

export default ProfileScreen;
