import { createStackNavigator } from '@react-navigation/stack';
import LoginScreen from '../screens/LoginScreen';
import CadastroScreen from '../screens/CadastroScreen';
import WelcomeScreen from '../screens/WelcomeScreen';
import ProductsScreen from '../screens/ProductsScreen';
import GestaoProdutosScreen from '../screens/GestaoProdutosScreen';
import ProfileScreen from '../screens/ProfileScreen';
import HistoricoScreen from '../screens/HistoricoScreen';
import HistoricoDetalhesScreen from '../screens/HistoricoDetalhesScreen';
import CadastroProdutoTela from '../screens/CadastroProdutoTela';
import EditarProdutoTela from '../screens/EditarProdutoTela';
import Autoatendimento from '../screens/Autoatendimento';
import VerPedidos from '../screens/VerPedidos';
import VendedorScreen from '../screens/VendedorScreen';
import FinalizarVendaScreen from '../screens/FinalizarVenda';
import Vendas from '../screens/Vendas';

const Stack = createStackNavigator();

export default function AppNavigator() {
  return (
    <Stack.Navigator 
      initialRouteName="Login"
      screenOptions={{
        headerBackTitleVisible: false
      }}
    >
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
          headerStyle: { backgroundColor: '#333' },
          headerTintColor: '#fff',
        }}
      />
      <Stack.Screen 
        name="GestaoProdutos" 
        component={GestaoProdutosScreen}
        options={{ 
          title: 'Gestão do Sistema',
          headerStyle: { backgroundColor: '#333' },
          headerTintColor: '#fff',
        }}
      />
      <Stack.Screen 
        name="ProfileScreen" 
        component={ProfileScreen}
        options={{
          title: 'Perfil',
          headerStyle: { backgroundColor: '#333' },
          headerTintColor: '#fff',
        }}
      />
      <Stack.Screen 
        name="Historico" 
        component={HistoricoScreen}
        options={{ 
          title: 'Histórico',
          headerStyle: { backgroundColor: '#333' },
          headerTintColor: '#fff',
        }}
      />
      <Stack.Screen 
        name="HistoricoDetalhes" 
        component={HistoricoDetalhesScreen}
        options={{ 
          title: 'Histórico Detalhes',
          headerStyle: { backgroundColor: '#333' },
          headerTintColor: '#fff',
        }}
      />
      <Stack.Screen 
        name="CadastroProduto" 
        component={CadastroProdutoTela}
        options={{ 
          title: 'Cadastro de Produto',
          headerStyle: { backgroundColor: '#333' },
          headerTintColor: '#fff',
        }}
      />
      <Stack.Screen 
        name="EditarProduto" 
        component={EditarProdutoTela}
        options={{ 
          title: 'Editar de Produto',
          headerStyle: { backgroundColor: '#333' },
          headerTintColor: '#fff',
        }}
      />
      <Stack.Screen 
        name="AutoAtendimento" 
        component={Autoatendimento}
        options={{ 
          title: 'Auto Atendimento',
          headerStyle: { backgroundColor: '#333' },
          headerTintColor: '#fff',
          gestureEnabled: false,
          headerLeft: () => null
        }}
      />
      <Stack.Screen 
        name="VerPedidos" 
        component={VerPedidos}
        options={{ 
          title: 'Ver Pedidos',
          headerStyle: { backgroundColor: '#333' },
          headerTintColor: '#fff',
        }}
      />

      <Stack.Screen 
        name="Vendedor" 
        component={VendedorScreen}
        options={{ 
          title: 'Vender Produtos',
          headerStyle: { backgroundColor: '#333' },
          headerTintColor: '#fff',
        }}
      />
      <Stack.Screen 
        name="FinalizarVenda" 
        component={FinalizarVendaScreen}
        options={{ 
          title: 'Finalizar Venda',
          headerStyle: { backgroundColor: '#333' },
          headerTintColor: '#fff',
        }}
      />
      <Stack.Screen 
        name="Vendas" 
        component={Vendas}
        options={{ 
          title: 'Visualziar Pedido da Venda',
          headerStyle: { backgroundColor: '#333' },
          headerTintColor: '#fff',
        }}
      />
    </Stack.Navigator>
  );
}