import 'package:flutter/material.dart';

/// Diálogo reutilizable para errores de red / servidor.
Future<void> showErrorDialog(BuildContext context, String message) {
  return showDialog<void>(
    context: context,
    builder: (ctx) => AlertDialog(
      title: const Text('Error'),
      content: Text(message),
      actions: [
        TextButton(
          onPressed: () => Navigator.of(ctx).pop(),
          child: const Text('Aceptar'),
        ),
      ],
    ),
  );
}
