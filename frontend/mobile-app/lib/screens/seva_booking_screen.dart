import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../models/models.dart';
import '../services/api_client.dart';
import '../services/temple_service.dart';

class SevaBookingScreen extends StatefulWidget {
  const SevaBookingScreen({super.key});

  @override
  State<SevaBookingScreen> createState() => _SevaBookingScreenState();
}

class _SevaBookingScreenState extends State<SevaBookingScreen> {
  late final TempleService _service;
  late Future<List<SevaService>> _sevasFuture;

  @override
  void initState() {
    super.initState();
    _service = TempleService(context.read<ApiClient>());
    _sevasFuture = _service.listSevas();
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Available Sevas', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
          const SizedBox(height: 12),
          Expanded(
            child: FutureBuilder<List<SevaService>>(
              future: _sevasFuture,
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return const Center(child: CircularProgressIndicator());
                }
                if (snapshot.hasError) {
                  return Center(child: Text('Failed to load sevas: ${snapshot.error}'));
                }
                final sevas = snapshot.data ?? [];
                if (sevas.isEmpty) {
                  return const Center(child: Text('No sevas available right now.'));
                }
                return ListView.builder(
                  itemCount: sevas.length,
                  itemBuilder: (context, i) {
                    final seva = sevas[i];
                    return Card(
                      child: ListTile(
                        title: Text(seva.name),
                        subtitle: Text('${seva.durationMinutes} min · ₹${seva.price.toStringAsFixed(0)}'),
                        trailing: FilledButton(
                          onPressed: () => _openSlotPicker(seva),
                          child: const Text('Book'),
                        ),
                      ),
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

  Future<void> _openSlotPicker(SevaService seva) async {
    final slots = await _service.listSlots(seva.id);
    if (!mounted) return;

    await showModalBottomSheet(
      context: context,
      builder: (context) {
        if (slots.isEmpty) {
          return const Padding(
            padding: EdgeInsets.all(24),
            child: Text('No open slots for this seva at the moment.'),
          );
        }
        return ListView(
          shrinkWrap: true,
          children: slots.map((slot) {
            return ListTile(
              title: Text('${slot.slotDate}  ${slot.startTime}–${slot.endTime}'),
              subtitle: Text('${slot.capacity - slot.bookedCount} spots left'),
              enabled: !slot.isFull,
              onTap: slot.isFull
                  ? null
                  : () {
                      Navigator.pop(context);
                      _confirmBooking(seva, slot);
                    },
            );
          }).toList(),
        );
      },
    );
  }

  Future<void> _confirmBooking(SevaService seva, SevaSlot slot) async {
    try {
      // devotee_id and temple_id would normally come from the signed-in
      // devotee's profile; using the demo devotee/temple seeded by the
      // backend for this scaffold.
      await _service.bookSeva(templeId: 1, devoteeId: 1, sevaId: seva.id, slotId: slot.id);
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('${seva.name} booked for ${slot.slotDate}')),
      );
    } on ApiException catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
    }
  }
}
