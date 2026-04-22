import 'package:equatable/equatable.dart';

import '../data/models/usuario_model.dart';

sealed class UsuarioState extends Equatable {
  const UsuarioState();

  @override
  List<Object?> get props => [];
}

class UsuarioInitial extends UsuarioState {
  const UsuarioInitial();
}

class UsuarioLoading extends UsuarioState {
  const UsuarioLoading();
}

class UsuarioLoadSuccess extends UsuarioState {
  const UsuarioLoadSuccess(this.usuarios);

  final List<UsuarioModel> usuarios;

  @override
  List<Object?> get props => [usuarios];
}

class UsuarioLoadFailure extends UsuarioState {
  const UsuarioLoadFailure(this.message);

  final String message;

  @override
  List<Object?> get props => [message];
}
