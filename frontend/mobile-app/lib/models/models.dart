class DevoteeUser {
  final int id;
  final String name;
  final String email;
  final int companyId;

  DevoteeUser({required this.id, required this.name, required this.email, required this.companyId});

  factory DevoteeUser.fromJson(Map<String, dynamic> json) => DevoteeUser(
        id: json['id'],
        name: json['name'],
        email: json['email'],
        companyId: json['company_id'],
      );
}

class Temple {
  final int id;
  final String name;
  final String? deityName;
  final String? city;

  Temple({required this.id, required this.name, this.deityName, this.city});

  factory Temple.fromJson(Map<String, dynamic> json) => Temple(
        id: json['id'],
        name: json['name'],
        deityName: json['deity_name'],
        city: json['city'],
      );
}

class SevaService {
  final int id;
  final String name;
  final String? description;
  final double price;
  final int durationMinutes;

  SevaService({
    required this.id,
    required this.name,
    this.description,
    required this.price,
    required this.durationMinutes,
  });

  factory SevaService.fromJson(Map<String, dynamic> json) => SevaService(
        id: json['id'],
        name: json['name'],
        description: json['description'],
        price: double.tryParse(json['price'].toString()) ?? 0,
        durationMinutes: json['duration_minutes'] ?? 30,
      );
}

class SevaSlot {
  final int id;
  final String slotDate;
  final String startTime;
  final String endTime;
  final int capacity;
  final int bookedCount;

  SevaSlot({
    required this.id,
    required this.slotDate,
    required this.startTime,
    required this.endTime,
    required this.capacity,
    required this.bookedCount,
  });

  bool get isFull => bookedCount >= capacity;

  factory SevaSlot.fromJson(Map<String, dynamic> json) => SevaSlot(
        id: json['id'],
        slotDate: json['slot_date'],
        startTime: json['start_time'],
        endTime: json['end_time'],
        capacity: json['capacity'],
        bookedCount: json['booked_count'],
      );
}

class SevaBooking {
  final int id;
  final String bookingId;
  final String status;
  final String paymentStatus;
  final String netAmount;

  SevaBooking({
    required this.id,
    required this.bookingId,
    required this.status,
    required this.paymentStatus,
    required this.netAmount,
  });

  factory SevaBooking.fromJson(Map<String, dynamic> json) => SevaBooking(
        id: json['id'],
        bookingId: json['booking_id'],
        status: json['status'],
        paymentStatus: json['payment_status'],
        netAmount: json['net_amount'].toString(),
      );
}

class Donation {
  final int id;
  final String donationId;
  final String amount;
  final String status;
  final String donationDate;

  Donation({
    required this.id,
    required this.donationId,
    required this.amount,
    required this.status,
    required this.donationDate,
  });

  factory Donation.fromJson(Map<String, dynamic> json) => Donation(
        id: json['id'],
        donationId: json['donation_id'],
        amount: json['amount'].toString(),
        status: json['status'],
        donationDate: json['donation_date'],
      );
}
