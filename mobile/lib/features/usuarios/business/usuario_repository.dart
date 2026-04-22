import '../data/models/usuario_model.dart';

/// Contrato de acceso a datos del dominio **Usuario** (la UI depende de esto, no de HTTP).
abstract class UsuarioRepository {
  Future<List<UsuarioModel>> obtenerUsuarios();
}
