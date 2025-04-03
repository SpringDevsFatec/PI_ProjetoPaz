import { createStackNavigator } from '@react-navigation/stack';
import LoginScreen from '../screens/LoginScreen';
import CadastroScreen from '../screens/CadastroScreen';
import WelcomeScreen from '../screens/WelcomeScreen';
import ProductsScreen from '../screens/ProductsScreen';

const Stack = createStackNavigator();

export default function AppNavigator() {
  return (
    <Stack.Navigator initialRouteName="Login">
      <Stack.Screen 
        name="Login" 
        component={LoginScreen} 
        options={{ headerShown: false }}
      />
      <Stack.Screen 
        name="Cadastro" 
        component={CadastroScreen}
        options={{ title: 'Criar Conta' }}
      />
      <Stack.Screen 
        name="Welcome" 
        component={WelcomeScreen}
        options={{ headerShown: false }}
      />
      <Stack.Screen 
        name="Products" 
        component={ProductsScreen}
        options={{ 
          title: 'Produtos',
          headerStyle: {
            backgroundColor: '#333',
          },
          headerTintColor: '#fff',
        }}
      />
    </Stack.Navigator>
  );
}