GAMEHUB – PHP + MySQL cho XAMPP

CÀI ĐẶT TRÊN WINDOWS
1. Mở XAMPP Control Panel, bấm Start ở Apache và MySQL.
2. Chép toàn bộ thư mục GameHub_XAMPP vào C:\xampp\htdocs\GameHub (hoặc thư mục htdocs của bạn).
3. Mở http://localhost/phpmyadmin
4. Chọn tab Import, chọn file schema.sql trong thư mục dự án rồi bấm Import/Go.
5. Mở config.php và sửa DB_USER / DB_PASS nếu MySQL của bạn không dùng root với mật khẩu trống.
6. Truy cập http://localhost/GameHub/ để đăng ký tài khoản đầu tiên.
7. Chép game HTML sẵn có vào thư mục games. Trang chủ tự quét các file .html trong thư mục đó.

LƯU ĐIỂM TỪ GAME
Game HTML không tự động gửi điểm nếu chưa tích hợp. Khi game tính được điểm cuối cùng, thêm đoạn này vào JavaScript của game (đổi 'memory_game' thành mã game riêng và thay score bằng biến điểm thực tế):

fetch('../save_score.php', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({game: 'memory_game', score: Number(score)})
})
.then(r => r.json())
.then(data => {
  if (data.ok) console.log('Điểm cao nhất đã lưu:', data.best_score);
  else console.warn(data.error || 'Không lưu được điểm');
});

Lưu ý: đoạn trên phù hợp khi game mở trực tiếp từ /GameHub/games/ và trang đang đăng nhập bằng cùng trình duyệt. Nếu file game dùng biến điểm khác, sửa biến score cho đúng. Không cho người chơi tự nhập điểm từ giao diện; với game cạnh tranh, cần xác thực điểm phía máy chủ vì người dùng có thể sửa JavaScript.

LƯU Ý QUAN TRỌNG VỀ GITHUB
GitHub Pages chỉ chạy website tĩnh (HTML/CSS/JS), không chạy PHP và không cung cấp MySQL. Có thể đưa mã nguồn lên GitHub, nhưng để người khác đăng ký tài khoản và lưu điểm chung, phải triển khai PHP + MySQL lên máy chủ hỗ trợ PHP/MySQL (hosting PHP/VPS). Database chỉ chạy trên máy tính XAMPP của bạn thì người ngoài Internet không thể dùng chung một cách an toàn. Không mở cổng router để lộ XAMPP trực tiếp.

TÀI KHOẢN VÀ DỮ LIỆU
- Tên tài khoản bắt đầu bằng @, ví dụ @minh_123.
- Tên tài khoản là duy nhất trong database.
- Mật khẩu được băm bằng password_hash, không lưu dạng chữ thường.
- Điểm số lưu trong bảng game_scores, giữ lại khi người dùng đăng xuất/đăng nhập lại.
- Xóa tài khoản sẽ xóa điểm số liên quan nhờ khóa ngoại ON DELETE CASCADE.
- Ảnh đại diện giới hạn 3 MB, chỉ JPG/PNG/WEBP.
- Mẫu này yêu cầu người dùng từ 13 tuổi trở lên. Điều chỉnh quy định nếu cần tuân thủ luật áp dụng cho dịch vụ của bạn.

TRƯỚC KHI ĐƯA LÊN INTERNET
Bật HTTPS, dùng mật khẩu database mạnh, tắt hiển thị lỗi PHP, đặt quyền thư mục uploads phù hợp, sao lưu database thường xuyên và kiểm tra bảo mật. Mẫu này là điểm khởi đầu, nên được kiểm thử và gia cố trước khi vận hành công khai.
