import 'package:equatable/equatable.dart';

/// Modelo de datos alineado al JSON del API Laravel (`UsuarioService::serializar`).
class UsuarioModel extends Equatable {
  const UsuarioModel({
    required this.id,
    required this.nombre,
    required this.email,
    this.createdAt,
    this.updatedAt,
  });

  final int id;
  final String nombre;
  final String email;
  final String? createdAt;
  final String? updatedAt;

  factory UsuarioModel.fromJson(Map<String, dynamic> json) {
    return UsuarioModel(
      id: json['id'] as int,
      nombre: json['nombre'] as String,
      email: json['email'] as String,
      createdAt: json['created_at'] as String?,
      updatedAt: json['updated_at'] as String?,
    );
  }

  @override
  List<Object?> get props => [id, nombre, email, createdAt, updatedAt];
}
