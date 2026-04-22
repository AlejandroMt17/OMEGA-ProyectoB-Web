/*
  Feature "usuarios": módulo autocontenido (MVVM + BLoC).

  - business: contratos de repositorio / reglas de dominio expuestas a la UI.
  - data: modelos, fuentes remotas y implementación del repositorio.
  - presentation: pantallas, widgets y diálogos (solo Flutter).
  - bloc: eventos, estados y UsuarioBloc (estado de pantalla).
*/

export 'bloc/usuario_bloc.dart';
export 'presentation/screens/usuario_list_screen.dart';
