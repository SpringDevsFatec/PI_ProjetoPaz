import { createStackNavigator } from '@react-navigation/stack';
import LoginScreen from '../screens/LoginScreen';
import CadastroScreen from '../screens/CadastroScreen';

const Stack = createStackNavigator();

export default function AppNavigator() {
  return (
    <Stack.Navigator initialRouteName="Login">
      <Stack.Screen 
        name="Login" 
        component={LoginScreen} 
        options={{ headerShown: false }} // Esconde a barra superior
      />
      <Stack.Screen 
        name="Cadastro" 
        component={CadastroScreen} 
        options={{ title: 'Criar Conta' }} // Título da tela
      />
    </Stack.Navigator>
  );
}