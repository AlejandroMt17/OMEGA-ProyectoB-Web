/// Contrato genérico para casos de uso (Clean Architecture / escalable).
///
/// Cada feature puede definir `class MiCasoDeUso implements UseCase<Salida, Entrada>`.
abstract class UseCase<Type, Params> {
  Future<Type> call(Params params);
}

/// Parámetro vacío cuando el caso de uso no recibe argumentos.
class NoParams {
  const NoParams();
}
