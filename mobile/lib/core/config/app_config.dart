/// Configuración de entorno: base URL del API Laravel.
///
/// Ajusta [apiBaseUrl] según dónde corre el backend (emulador Android usa
/// `http://10.0.2.2:8000` para apuntar al host; iOS simulador suele usar `localhost`).
class AppConfig {
  AppConfig._();

  /// Sin barra final. El prefijo `/api` se añade en [ApiConstants].
  static const String apiBaseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://10.0.2.2:8000',
  );
}
