import 'package:flutter/material.dart';

import '../../data/models/usuario_model.dart';

/// Fila de lista para un usuario (presentación pura).
class UsuarioListTile extends StatelessWidget {
  const UsuarioListTile({super.key, required this.usuario});

  final UsuarioModel usuario;

  @override
  Widget build(BuildContext context) {
    return ListTile(
      leading: CircleAvatar(
        child: Text(usuario.nombre.isNotEmpty ? usuario.nombre[0].toUpperCase() : '?'),
      ),
      title: Text(usuario.nombre),
      subtitle: Text(usuario.email),
    );
  }
}
