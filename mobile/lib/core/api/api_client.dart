import 'dart:convert';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:http/http.dart' as http;

class ApiClient {
  ApiClient(this.baseUrl, {http.Client? client}) : _client = client ?? http.Client();
  final String baseUrl;
  final http.Client _client;
  final _storage = const FlutterSecureStorage();

  Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await _client.post(Uri.parse('$baseUrl/api/v1/auth/login'), headers: {'Accept': 'application/json'}, body: {'email': email, 'password': password});
    final data = _decode(response);
    await _storage.write(key: 'mizan3g_token', value: data['token'] as String);
    return data;
  }

  Future<dynamic> get(String path) async {
    final token = await _storage.read(key: 'mizan3g_token');
    final response = await _client.get(Uri.parse('$baseUrl/api/v1$path'), headers: {'Accept': 'application/json', if (token != null) 'Authorization': 'Bearer $token'});
    return _decode(response);
  }

  dynamic _decode(http.Response response) {
    final data = jsonDecode(response.body);
    if (response.statusCode < 200 || response.statusCode >= 300) throw ApiException(data['message']?.toString() ?? 'Request failed');
    return data;
  }
}

class ApiException implements Exception { ApiException(this.message); final String message; }
