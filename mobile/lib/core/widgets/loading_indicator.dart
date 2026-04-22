import 'package:flutter/material.dart';

/// Indicador de carga reutilizable (evita duplicar [CircularProgressIndicator]).
class LoadingIndicator extends StatelessWidget {
  const LoadingIndicator({super.key});

  @override
  Widget build(BuildContext context) {
    return const Center(
      child: CircularProgressIndicator(),
    );
  }
}
