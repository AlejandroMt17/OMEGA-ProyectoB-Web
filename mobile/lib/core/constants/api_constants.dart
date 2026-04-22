/// Rutas y cabeceras del API REST (Laravel `routes/api.php`).
class ApiConstants {
  ApiConstants._();

  static const String apiPrefix = '/api';
  static const String usuarios = '$apiPrefix/usuarios';

  static Map<String, String> jsonHeaders = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  };
}
