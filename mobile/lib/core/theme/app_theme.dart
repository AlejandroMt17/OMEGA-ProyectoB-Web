import 'package:flutter/material.dart';

/// Tema global; los features pueden sobrescribir estilos locales si hace falta.
class AppTheme {
  AppTheme._();

  static ThemeData light() {
    const seed = Color(0xFF1565C0);
    return ThemeData(
      colorScheme: ColorScheme.fromSeed(seedColor: seed),
      useMaterial3: true,
      appBarTheme: const AppBarTheme(centerTitle: true),
    );
  }
}
