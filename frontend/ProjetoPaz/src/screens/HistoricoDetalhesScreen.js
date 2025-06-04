import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Image, Dimensions, ScrollView } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { PieChart } from 'react-native-chart-kit';

const HistoricoDetalhesScreen = ({ navigation, route }) => {
  const dataVenda = route.params?.data || "00/00/0000";
  const estoqueInicial = route.params?.estoqueInicial || 20;
  const estoqueFinal = route.params?.estoqueFinal || 14;
  const totalVendido = route.params?.totalVendido || 6;
  const totalArrecadado = route.params?.totalArrecadado || "20$";
  const totalLucro = route.params?.totalLucro || "15$";
  
  // Dados de produtos vendidos (exemplo)
  const produtosVendidos = route.params?.produtosVendidos || {
    "Produto A": 3,
    "Produto B": 2,
    "Produto C": 1
  };

  // Dados para o gráfico de pizza (itens mais vendidos)
  const pieData = Object.entries(produtosVendidos).map(([nome, quantidade], index) => ({
    name: nome,
    value: quantidade,
    color: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"][index % 5],
    legendFontColor: "#333",
    legendFontSize: 12
  }));

  const handleBack = () => {
    navigation.goBack();
  };

  return (
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.gradient}>
      <View style={styles.header}>
        <TouchableOpacity onPress={handleBack}>
          <Ionicons name="arrow-back-outline" size={28} color="#333" />
        </TouchableOpacity>
        <Image source={require('../../assets/images/logopaz.png')} style={styles.logo} />
        <View style={{ width: 28 }} />
      </View>

      <ScrollView contentContainerStyle={styles.container}>
        {/* Título */}
        <Text style={styles.title}>Histórico de vendas do dia</Text>
        <Text style={styles.subtitle}>{dataVenda}</Text>

        {/* Gráfico de Pizza - Itens mais vendidos */}
        <View style={styles.chartContainer}>
          <Text style={styles.chartTitle}>Itens mais vendidos</Text>
          <PieChart
            data={pieData}
            width={Dimensions.get('window').width - 40}
            height={220}
            chartConfig={{
              color: (opacity = 1) => `rgba(0, 0, 0, ${opacity})`,
            }}
            accessor="value"
            backgroundColor="transparent"
            paddingLeft="15"
            absolute
            hasLegend={true}
          />
        </View>

        {/* Seção de Estoque */}
        <Text style={styles.sectionTitle}>Estoque do dia</Text>
        <View style={styles.stockContainer}>
          <View style={styles.stockItem}>
            <Text style={styles.stockText}>Estoque Inicial:</Text>
            <Text style={styles.stockValue}>{estoqueInicial}</Text>
          </View>
          <View style={styles.stockItem}>
            <Text style={styles.stockText}>Estoque Final:</Text>
            <Text style={styles.stockValue}>{estoqueFinal}</Text>
          </View>
        </View>

        {/* Relatório Geral */}
        <Text style={styles.sectionTitle}>Relatório Geral</Text>
        <View style={styles.reportContainer}>
          <View style={styles.reportItem}>
            <Text style={styles.reportText}>Total vendido:</Text>
            <Text style={styles.reportValue}>{totalVendido}</Text>
          </View>
          <View style={styles.reportItem}>
            <Text style={styles.reportText}>Total arrecadado:</Text>
            <Text style={styles.reportValue}>{totalArrecadado}</Text>
          </View>
          <View style={styles.reportItem}>
            <Text style={styles.reportText}>Total de lucro:</Text>
            <Text style={styles.reportValue}>{totalLucro}</Text>
          </View>
        </View>
      </ScrollView>

      {/* Rodapé fixo */}
      <View style={styles.footer}>
        <Ionicons name="home-outline" size={24} color="white" />
      </View>
    </LinearGradient>
  );
};

const styles = StyleSheet.create({
  gradient: {
    flex: 1,
  },
  container: {
    paddingTop: 20,
    paddingHorizontal: 20,
    paddingBottom: 100,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingTop: 50,
    paddingBottom: 10,
    backgroundColor: 'transparent',
    zIndex: 1,
  },
  logo: {
    width: 70,
    height: 70,
    resizeMode: 'contain',
  },
  title: {
    fontSize: 18,
    fontWeight: 'bold',
    alignSelf: 'center',
    marginBottom: 5,
  },
  subtitle: {
    fontSize: 14,
    alignSelf: 'center',
    marginBottom: 20,
    color: '#555',
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    marginTop: 15,
    marginBottom: 10,
    color: '#333',
  },
  chartContainer: {
    backgroundColor: '#fff',
    borderRadius: 10,
    padding: 15,
    borderWidth: 1,
    borderColor: '#ddd',
    marginBottom: 15,
    alignItems: 'center',
  },
  chartTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 10,
    color: '#333',
    alignSelf: 'flex-start',
  },
  stockContainer: {
    backgroundColor: '#fff',
    borderRadius: 10,
    padding: 15,
    borderWidth: 1,
    borderColor: '#ddd',
  },
  stockItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginVertical: 5,
  },
  stockText: {
    fontSize: 14,
    color: '#555',
  },
  stockValue: {
    fontSize: 14,
    fontWeight: 'bold',
  },
  reportContainer: {
    backgroundColor: '#fff',
    borderRadius: 10,
    padding: 15,
    borderWidth: 1,
    borderColor: '#ddd',
    marginBottom: 20,
  },
  reportItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginVertical: 5,
  },
  reportText: {
    fontSize: 14,
    color: '#555',
  },
  reportValue: {
    fontSize: 14,
    fontWeight: 'bold',
  },
  footer: {
    position: 'absolute',
    bottom: 0,
    width: '100%',
    backgroundColor: '#333',
    paddingVertical: 10,
    alignItems: 'center',
    borderTopLeftRadius: 15,
    borderTopRightRadius: 15,
  },
});

export default HistoricoDetalhesScreen;