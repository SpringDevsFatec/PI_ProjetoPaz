import * as ImagePicker from 'expo-image-picker';
import { Alert } from 'react-native';

// Configurações para validação de imagens
const IMAGE_CONFIG = {
  maxSizeInMB: 5, // Limite de 5MB
  allowedTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'],
  quality: 0.8, // Qualidade da compressão
};

/**
 * Converte uma imagem para base64
 * @param {string} uri - URI da imagem
 * @returns {Promise<string>} - String base64 da imagem
 */
export const convertImageToBase64 = async (uri) => {
  try {
    const response = await fetch(uri);
    const blob = await response.blob();
    
    return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onloadend = () => {
        const base64String = reader.result.split(',')[1]; // Remove o prefixo data:image/...;base64,
        resolve(base64String);
      };
      reader.onerror = reject;
      reader.readAsDataURL(blob);
    });
  } catch (error) {
    throw new Error('Erro ao converter imagem para base64: ' + error.message);
  }
};

/**
 * Valida se o arquivo é uma imagem válida
 * @param {Object} imageInfo - Informações da imagem do ImagePicker
 * @returns {boolean} - True se válida, false caso contrário
 */
export const validateImage = (imageInfo) => {
  if (!imageInfo || !imageInfo.uri) {
    Alert.alert('Erro', 'Imagem inválida');
    return false;
  }

  // Verifica o tamanho do arquivo
  if (imageInfo.fileSize) {
    const sizeInMB = imageInfo.fileSize / (1024 * 1024);
    if (sizeInMB > IMAGE_CONFIG.maxSizeInMB) {
      Alert.alert(
        'Arquivo muito grande', 
        `A imagem deve ter no máximo ${IMAGE_CONFIG.maxSizeInMB}MB. Tamanho atual: ${sizeInMB.toFixed(2)}MB`
      );
      return false;
    }
  }

  // Verifica o tipo do arquivo
  if (imageInfo.type && !IMAGE_CONFIG.allowedTypes.includes(imageInfo.type)) {
    Alert.alert(
      'Formato não suportado', 
      'Apenas imagens nos formatos JPEG, PNG, GIF e WebP são permitidas'
    );
    return false;
  }

  return true;
};

/**
 * Abre o seletor de imagens com validação
 * @param {Object} options - Opções do ImagePicker
 * @returns {Promise<Object>} - Resultado com URI e base64 da imagem
 */
export const pickImageWithValidation = async (options = {}) => {
  try {
    // Solicita permissão para acessar a galeria
    const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (status !== 'granted') {
      Alert.alert('Permissão negada', 'É necessário permitir o acesso à galeria para selecionar imagens');
      return null;
    }

    const defaultOptions = {
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsEditing: true,
      aspect: [1, 1],
      quality: IMAGE_CONFIG.quality,
      allowsMultipleSelection: false,
    };

    const result = await ImagePicker.launchImageLibraryAsync({
      ...defaultOptions,
      ...options,
    });

    if (result.canceled) {
      return null;
    }

    const imageInfo = result.assets[0];
    
    // Valida a imagem
    if (!validateImage(imageInfo)) {
      return null;
    }

    // Converte para base64
    const base64 = await convertImageToBase64(imageInfo.uri);

    return {
      uri: imageInfo.uri,
      base64: base64,
      type: imageInfo.type || 'image/jpeg',
      size: imageInfo.fileSize,
    };
  } catch (error) {
    Alert.alert('Erro', 'Falha ao selecionar imagem: ' + error.message);
    return null;
  }
};

/**
 * Abre a câmera com validação
 * @param {Object} options - Opções do ImagePicker
 * @returns {Promise<Object>} - Resultado com URI e base64 da imagem
 */
export const takePictureWithValidation = async (options = {}) => {
  try {
    // Solicita permissão para usar a câmera
    const { status } = await ImagePicker.requestCameraPermissionsAsync();
    if (status !== 'granted') {
      Alert.alert('Permissão negada', 'É necessário permitir o acesso à câmera para tirar fotos');
      return null;
    }

    const defaultOptions = {
      allowsEditing: true,
      aspect: [1, 1],
      quality: IMAGE_CONFIG.quality,
    };

    const result = await ImagePicker.launchCameraAsync({
      ...defaultOptions,
      ...options,
    });

    if (result.canceled) {
      return null;
    }

    const imageInfo = result.assets[0];
    
    // Valida a imagem
    if (!validateImage(imageInfo)) {
      return null;
    }

    // Converte para base64
    const base64 = await convertImageToBase64(imageInfo.uri);

    return {
      uri: imageInfo.uri,
      base64: base64,
      type: imageInfo.type || 'image/jpeg',
      size: imageInfo.fileSize,
    };
  } catch (error) {
    Alert.alert('Erro', 'Falha ao tirar foto: ' + error.message);
    return null;
  }
};

/**
 * Mostra opções para selecionar imagem (câmera ou galeria)
 * @returns {Promise<Object>} - Resultado com URI e base64 da imagem
 */
export const showImagePickerOptions = () => {
  return new Promise((resolve) => {
    Alert.alert(
      'Selecionar Imagem',
      'Escolha uma opção:',
      [
        {
          text: 'Tirar Foto',
          onPress: async () => {
            const result = await takePictureWithValidation();
            resolve(result);
          },
        },
        {
          text: 'Escolher da Galeria',
          onPress: async () => {
            const result = await pickImageWithValidation();
            resolve(result);
          },
        },
        {
          text: 'Cancelar',
          style: 'cancel',
          onPress: () => resolve(null),
        },
      ],
      { cancelable: true, onDismiss: () => resolve(null) }
    );
  });
};

