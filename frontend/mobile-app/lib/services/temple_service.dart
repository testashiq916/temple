import '../models/models.dart';
import 'api_client.dart';

/// Devotee-facing read/write operations: browse sevas, book slots, view
/// donation history. Mirrors the endpoints consumed by the web-admin app,
/// scoped server-side to the logged-in devotee's own records.
class TempleService {
  final ApiClient client;
  TempleService(this.client);

  Future<List<Temple>> listTemples() async {
    final json = await client.get('/temples');
    return (json['data'] as List).map((e) => Temple.fromJson(e)).toList();
  }

  Future<List<SevaService>> listSevas({int? templeId}) async {
    final json = await client.get('/sevas', query: templeId != null ? {'temple_id': templeId} : null);
    return (json['data'] as List).map((e) => SevaService.fromJson(e)).toList();
  }

  Future<List<SevaSlot>> listSlots(int sevaId, {String? date}) async {
    final json = await client.get('/sevas/$sevaId/slots', query: date != null ? {'date': date} : null);
    return (json as List).map((e) => SevaSlot.fromJson(e)).toList();
  }

  Future<SevaBooking> bookSeva({
    required int templeId,
    required int devoteeId,
    required int sevaId,
    required int slotId,
    int numberOfDevotees = 1,
  }) async {
    final json = await client.post('/seva-bookings', {
      'temple_id': templeId,
      'devotee_id': devoteeId,
      'seva_id': sevaId,
      'slot_id': slotId,
      'number_of_devotees': numberOfDevotees,
    });
    return SevaBooking.fromJson(json);
  }

  Future<List<SevaBooking>> myBookings() async {
    final json = await client.get('/seva-bookings');
    return (json['data'] as List).map((e) => SevaBooking.fromJson(e)).toList();
  }

  Future<List<Donation>> myDonations() async {
    final json = await client.get('/donations');
    return (json['data'] as List).map((e) => Donation.fromJson(e)).toList();
  }

  Future<Donation> donate({
    required int templeId,
    required int donorId,
    required double amount,
    String paymentMethod = 'upi',
    String? purpose,
  }) async {
    final json = await client.post('/donations', {
      'temple_id': templeId,
      'donor_id': donorId,
      'amount': amount,
      'payment_method': paymentMethod,
      if (purpose != null) 'purpose': purpose,
    });
    return Donation.fromJson(json);
  }
}
