# Documentação das Modificações - Projeto Paz

## Resumo das Alterações

Este documento descreve as modificações implementadas no projeto PI_ProjetoPaz para suportar requisições com imagens em base64 via headers e validação de upload de imagens.

## Arquivos Modificados

### 1. `/src/services/api.js`
- **Adicionado**: Funções para requisições com imagens em base64
- **Funcionalidades**:
  - `apiWithImage()`: Função genérica para requisições com imagem em base64 via header
  - `cadastrarProdutoComImagem()`: Função específica para cadastro de produtos
  - `atualizarProdutoComImagem()`: Função específica para atualização de produtos
  - Headers utilizados: `X-Image-Data` (base64) e `X-Image-Type` (tipo da imagem)

### 2. `/src/utils/imageUtils.js` (NOVO)
- **Criado**: Utilitários para validação e manipulação de imagens
- **Funcionalidades**:
  - Validação de tipo de arquivo (apenas imagens: JPEG, PNG, GIF, WebP)
  - Limite de tamanho: 5MB máximo
  - Conversão automática para base64
  - Integração com expo-image-picker
  - Solicitação automática de permissões (câmera e galeria)

### 3. `/src/screens/CadastroProdutoTela.js`
- **Modificado**: Implementação de upload de imagens com validação
- **Alterações**:
  - Adicionado estado `imagemSelecionada` para armazenar dados da imagem
  - Função `editarImagem()` atualizada para usar `showImagePickerOptions()`
  - Função `cadastrarProduto()` atualizada para enviar imagem via base64
  - Interface atualizada para mostrar preview da imagem selecionada
  - Adicionado loading state durante o cadastro

### 4. `/src/screens/EditarProdutoTela.js`
- **Modificado**: Implementação de upload de imagens com validação
- **Alterações**:
  - Adicionado estado `imagemSelecionada` para novas imagens
  - Função `editarImagem()` atualizada para usar `showImagePickerOptions()`
  - Função `salvarAlteracoes()` atualizada para enviar imagem via base64
  - Interface atualizada para mostrar preview da nova imagem ou imagem existente
  - Prioridade: nova imagem selecionada > imagem existente > placeholder

## Configurações de Validação

### Tipos de Arquivo Permitidos
- JPEG/JPG
- PNG
- GIF
- WebP

### Limites de Tamanho
- **Máximo**: 5MB por imagem
- **Qualidade de compressão**: 80% (configurável)

### Headers HTTP Utilizados
- `X-Image-Data`: String base64 da imagem (sem prefixo data:image)
- `X-Image-Type`: Tipo MIME da imagem (ex: "image/jpeg")

## Dependências Necessárias

Para que as modificações funcionem corretamente, certifique-se de que as seguintes dependências estão instaladas:

```bash
npm install expo-image-picker
```

## Estrutura de Dados

### Objeto de Imagem Retornado
```javascript
{
  uri: "file://...",           // URI local da imagem
  base64: "iVBORw0KGgoA...",   // String base64 (sem prefixo)
  type: "image/jpeg",          // Tipo MIME
  size: 1024000               // Tamanho em bytes
}
```

### Headers da Requisição
```javascript
{
  "Content-Type": "application/json",
  "Authorization": "Bearer <token>",
  "X-Image-Data": "<base64_string>",
  "X-Image-Type": "image/jpeg"
}
```

## Implementação no Backend

O backend deve estar preparado para:

1. **Receber headers de imagem**:
   - `X-Image-Data`: String base64 da imagem
   - `X-Image-Type`: Tipo MIME da imagem

2. **Processar a imagem**:
   - Decodificar base64
   - Validar tipo e tamanho
   - Salvar no sistema de arquivos ou storage
   - Retornar URL da imagem salva

3. **Exemplo de implementação (Node.js/Express)**:
```javascript
app.post('/produtos', (req, res) => {
  const imageData = req.headers['x-image-data'];
  const imageType = req.headers['x-image-type'];
  
  if (imageData) {
    // Processar imagem base64
    const buffer = Buffer.from(imageData, 'base64');
    // Salvar arquivo e obter URL
    const imageUrl = saveImageFile(buffer, imageType);
    req.body.imagemUrl = imageUrl;
  }
  
  // Processar dados do produto
  // ...
});
```

## Funcionalidades Implementadas

### ✅ Validação de Upload
- Apenas arquivos de imagem são aceitos
- Limite de tamanho de 5MB
- Mensagens de erro informativas

### ✅ Conversão Base64
- Conversão automática para base64
- Envio via headers HTTP
- Suporte a diferentes tipos de imagem

### ✅ Interface de Usuário
- Preview da imagem selecionada
- Opções de câmera e galeria
- Estados de loading
- Feedback visual adequado

### ✅ Integração com API
- Funções específicas para produtos
- Compatibilidade com sistema de autenticação existente
- Tratamento de erros

## Próximos Passos

1. **Backend**: Implementar o processamento dos headers de imagem
2. **Testes**: Testar upload com diferentes tipos e tamanhos de imagem
3. **Otimização**: Considerar compressão adicional para imagens grandes
4. **Cache**: Implementar cache local para imagens já carregadas

## Notas Importantes

- As modificações são compatíveis com o código existente
- Não há breaking changes nas funcionalidades atuais
- O sistema funciona com ou sem imagens
- Todas as validações são feitas no frontend antes do envio

