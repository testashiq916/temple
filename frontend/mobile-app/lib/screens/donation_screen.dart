import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../models/models.dart';
import '../services/api_client.dart';
import '../services/temple_service.dart';

class DonationScreen extends StatefulWidget {
  const DonationScreen({super.key});

  @override
  State<DonationScreen> createState() => _DonationScreenState();
}

class _DonationScreenState extends State<DonationScreen> {
  late final TempleService _service;
  late Future<List<Donation>> _donationsFuture;
  final _amountController = TextEditingController();
  bool _submitting = false;

  @override
  void initState() {
    super.initState();
    _service = TempleService(context.read<ApiClient>());
    _donationsFuture = _service.myDonations();
  }

  void _refresh() => setState(() => _donationsFuture = _service.myDonations());

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Make a Donation', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: TextField(
                  controller: _amountController,
                  keyboardType: const TextInputType.numberWithOptions(decimal: true),
                  decoration: const InputDecoration(labelText: 'Amount (₹)', border: OutlineInputBorder()),
                ),
              ),
              const SizedBox(width: 12),
              FilledButton(
                onPressed: _submitting ? null : _donate,
                child: _submitting
                    ? const SizedBox(height: 16, width: 16, child: CircularProgressIndicator(strokeWidth: 2))
                    : const Text('Donate'),
              ),
            ],
          ),
          const SizedBox(height: 24),
          const Text('My Donations', style: TextStyle(fontWeight: FontWeight.bold)),
          const SizedBox(height: 8),
          Expanded(
            child: FutureBuilder<List<Donation>>(
              future: _donationsFuture,
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return const Center(child: CircularProgressIndicator());
                }
                final donations = snapshot.data ?? [];
                if (donations.isEmpty) {
                  return const Center(child: Text('No donations yet.'));
                }
                return ListView.builder(
                  itemCount: donations.length,
                  itemBuilder: (context, i) {
                    final d = donations[i];
                    return ListTile(
                      leading: const Icon(Icons.receipt_long_outlined),
                      title: Text('₹${d.amount}'),
                      subtitle: Text('${d.donationDate} · ${d.status}'),
                    );
                  },
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Future<void> _donate() async {
    final amount = double.tryParse(_amountController.text);
    if (amount == null || amount <= 0) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Enter a valid amount')));
      return;
    }
    setState(() => _submitting = true);
    try {
      // donor_id/temple_id come from the devotee's linked donor profile;
      // using the demo donor/temple seeded by the backend for this scaffold.
      await _service.donate(templeId: 1, donorId: 1, amount: amount);
      _amountController.clear();
      _refresh();
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Thank you for your donation 🙏')));
    } on ApiException catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
    } finally {
      if (mounted) setState(() => _submitting = false);
    }
  }
}
