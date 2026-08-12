import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../services/auth_provider.dart';
import 'seva_booking_screen.dart';
import 'donation_screen.dart';
import 'profile_screen.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  int _index = 0;

  static const _screens = [
    _DashboardHome(),
    SevaBookingScreen(),
    DonationScreen(),
    ProfileScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(child: _screens[_index]),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _index,
        onDestinationSelected: (i) => setState(() => _index = i),
        destinations: const [
          NavigationDestination(icon: Icon(Icons.home_outlined), selectedIcon: Icon(Icons.home), label: 'Home'),
          NavigationDestination(
              icon: Icon(Icons.self_improvement_outlined), selectedIcon: Icon(Icons.self_improvement), label: 'Seva'),
          NavigationDestination(
              icon: Icon(Icons.volunteer_activism_outlined),
              selectedIcon: Icon(Icons.volunteer_activism),
              label: 'Donate'),
          NavigationDestination(icon: Icon(Icons.person_outline), selectedIcon: Icon(Icons.person), label: 'Profile'),
        ],
      ),
    );
  }
}

class _DashboardHome extends StatelessWidget {
  const _DashboardHome();

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;

    return Padding(
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Welcome, ${user?.name ?? 'Devotee'}', style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
          const SizedBox(height: 4),
          const Text('May your day be blessed 🙏', style: TextStyle(color: Colors.black54)),
          const SizedBox(height: 24),
          Card(
            child: ListTile(
              leading: const Icon(Icons.self_improvement, color: Color(0xFFC2410C)),
              title: const Text('Book a Seva'),
              subtitle: const Text('Reserve a slot for rituals and pooja services'),
            ),
          ),
          Card(
            child: ListTile(
              leading: const Icon(Icons.volunteer_activism, color: Color(0xFFC2410C)),
              title: const Text('Make a Donation'),
              subtitle: const Text('Support the temple with a contribution'),
            ),
          ),
        ],
      ),
    );
  }
}
