import 'package:equatable/equatable.dart';

sealed class UsuarioEvent extends Equatable {
  const UsuarioEvent();

  @override
  List<Object?> get props => [];
}

/// Carga la lista desde el API.
class UsuarioLoadRequested extends UsuarioEvent {
  const UsuarioLoadRequested();
}
