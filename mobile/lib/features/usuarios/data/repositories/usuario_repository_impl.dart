import '../../business/usuario_repository.dart';
import '../datasources/usuario_remote_datasource.dart';
import '../models/usuario_model.dart';

/// Implementación del repositorio: delega en la fuente remota (inyectable / testeable).
class UsuarioRepositoryImpl implements UsuarioRepository {
  UsuarioRepositoryImpl({required UsuarioRemoteDatasource remote})
      : _remote = remote;

  final UsuarioRemoteDatasource _remote;

  @override
  Future<List<UsuarioModel>> obtenerUsuarios() => _remote.fetchUsuarios();
}
