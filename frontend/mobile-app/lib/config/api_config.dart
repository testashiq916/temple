/// Base URL for the Temple ERP Laravel API. Override at build time with:
///   flutter run --dart-define=API_BASE_URL=https://api.example.com/api/v1
class ApiConfig {
  static const String baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://10.0.2.2:8000/api/v1',
  );
}
