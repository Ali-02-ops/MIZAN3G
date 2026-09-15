import 'package:flutter/material.dart';
import 'core/api/api_client.dart';

void main() => runApp(const MizanApp());

class MizanApp extends StatelessWidget {
  const MizanApp({super.key});
  @override Widget build(BuildContext context) => MaterialApp(title: 'MIZAN3G', theme: ThemeData(colorSchemeSeed: const Color(0xff103b32)), home: const LoginScreen());
}

class LoginScreen extends StatefulWidget { const LoginScreen({super.key}); @override State<LoginScreen> createState() => _LoginScreenState(); }
class _LoginScreenState extends State<LoginScreen> {
  final email = TextEditingController(); final password = TextEditingController();
  Future<void> submit() async { await ApiClient(const String.fromEnvironment('MIZAN3G_API_URL', defaultValue: 'http://10.0.2.2:8001')).login(email.text, password.text); if (mounted) Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => const ProjectScreen())); }
  @override Widget build(BuildContext context) => Scaffold(body: Padding(padding: const EdgeInsets.all(24), child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [const Text('MIZAN3G', style: TextStyle(fontSize: 30)), TextField(controller: email, decoration: const InputDecoration(labelText: 'Email')), TextField(controller: password, obscureText: true, decoration: const InputDecoration(labelText: 'Password')), FilledButton(onPressed: submit, child: const Text('Sign in'))])));
}
class ProjectScreen extends StatelessWidget { const ProjectScreen({super.key}); @override Widget build(BuildContext context) => const Scaffold(appBar: AppBar(title: Text('MIZAN3G projects')), body: Center(child: Text('Projects, assigned audits, reviews, results, and reports are loaded from the Laravel API.'))); }
