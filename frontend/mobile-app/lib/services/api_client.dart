import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_config.dart';

class ApiException implements Exception {
  final String message;
  ApiException(this.message);

  @override
  String toString() => message;
}

/// Thin JSON/REST wrapper around the Temple ERP Laravel API. Persists the
/// Sanctum bearer token in SharedPreferences so the devotee stays signed in
/// across app restarts.
class ApiClient {
  static const _tokenKey = 'temple_erp_token';

  Future<String?> get token async => (await SharedPreferences.getInstance()).getString(_tokenKey);

  Future<void> saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_tokenKey, token);
  }

  Future<void> clearToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_tokenKey);
  }

  Future<Map<String, String>> _headers() async {
    final t = await token;
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (t != null) 'Authorization': 'Bearer $t',
    };
  }

  Uri _uri(String path, [Map<String, dynamic>? query]) =>
      Uri.parse('${ApiConfig.baseUrl}$path').replace(queryParameters: query?.map((k, v) => MapEntry(k, '$v')));

  Future<dynamic> get(String path, {Map<String, dynamic>? query}) async {
    final res = await http.get(_uri(path, query), headers: await _headers());
    return _handle(res);
  }

  Future<dynamic> post(String path, [Map<String, dynamic>? body]) async {
    final res = await http.post(_uri(path), headers: await _headers(), body: jsonEncode(body ?? {}));
    return _handle(res);
  }

  dynamic _handle(http.Response res) {
    if (res.statusCode >= 200 && res.statusCode < 300) {
      if (res.body.isEmpty) return null;
      return jsonDecode(res.body);
    }
    try {
      final decoded = jsonDecode(res.body);
      throw ApiException(decoded['message'] ?? 'Request failed (${res.statusCode})');
    } catch (_) {
      throw ApiException('Request failed (${res.statusCode})');
    }
  }
}
