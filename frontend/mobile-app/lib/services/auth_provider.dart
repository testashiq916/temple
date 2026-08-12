import 'package:flutter/foundation.dart';
import '../models/models.dart';
import 'api_client.dart';

class AuthProvider extends ChangeNotifier {
  final ApiClient _client;
  DevoteeUser? user;
  bool loading = true;
  String? error;

  AuthProvider(this._client) {
    _restoreSession();
  }

  Future<void> _restoreSession() async {
    final token = await _client.token;
    if (token == null) {
      loading = false;
      notifyListeners();
      return;
    }
    try {
      final json = await _client.get('/auth/me');
      user = DevoteeUser.fromJson(json);
    } catch (_) {
      await _client.clearToken();
    } finally {
      loading = false;
      notifyListeners();
    }
  }

  Future<bool> login(String email, String password) async {
    error = null;
    try {
      final json = await _client.post('/auth/login', {'email': email, 'password': password});
      await _client.saveToken(json['token']);
      user = DevoteeUser.fromJson(json['user']);
      notifyListeners();
      return true;
    } on ApiException catch (e) {
      error = e.message;
      notifyListeners();
      return false;
    }
  }

  Future<void> logout() async {
    try {
      await _client.post('/auth/logout');
    } catch (_) {
      // best-effort: clear local session regardless of API result
    }
    await _client.clearToken();
    user = null;
    notifyListeners();
  }
}
