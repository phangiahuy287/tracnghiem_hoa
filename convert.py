import re
import os

print("=== BẮT ĐẦU XỬ LÝ DỮ LIỆU CÓ ĐÁP ÁN ===")

if not os.path.exists('data.txt'):
    print("LỖI: Không tìm thấy file 'data.txt'.")
    exit()

with open('data.txt', 'r', encoding='utf-8') as file:
    text = file.read()

print(f"1. Đã đọc file data.txt. Độ dài văn bản: {len(text)} ký tự.")

# Regex tìm chương
chapter_pattern = r'Lớp (\d+) - Chương (\d+):([^\n(]+)'
chapters = list(re.finditer(chapter_pattern, text))

print(f"2. Tìm thấy {len(chapters)} tiêu đề chương.")

if len(chapters) == 0:
    print("CẢNH BÁO: Không tìm thấy tiêu đề chương nào! Hãy kiểm tra lại file data.txt")
    exit()

sql_statements = []

for i in range(len(chapters)):
    lop = chapters[i].group(1)
    chuong = "Chương " + chapters[i].group(2) + ": " + chapters[i].group(3).strip()
    hoc_ki = 1 if int(chapters[i].group(2)) <= 4 else 2
    
    start_idx = chapters[i].end()
    end_idx = chapters[i+1].start() if i + 1 < len(chapters) else len(text)
    chapter_text = text[start_idx:end_idx]
    
    # Regex tách Câu hỏi, 4 Đáp án A B C D và Đáp án (Cập nhật Regex mới)
    q_pattern = r'Câu \d+:(.*?)\s*A\.(.*?)\s*B\.(.*?)\s*C\.(.*?)\s*D\.(.*?)\s*Đáp án:\s*(.*?)(?=Câu \d+:|$)'
    questions = re.findall(q_pattern, chapter_text, re.DOTALL)
    
    for q in questions:
        noidung = q[0].strip().replace("'", "\\'")
        ans_a = q[1].strip().replace("'", "\\'")
        ans_b = q[2].strip().replace("'", "\\'")
        ans_c = q[3].strip().replace("'", "\\'")
        ans_d = q[4].strip().replace("'", "\\'")
        
        # Bóc tách đáp án (Lấy chữ cái đầu tiên trong chuỗi đáp án)
        raw_answer = q[5].strip()
        dapan_dung = 'A' # Mặc định dự phòng
        match_ans = re.search(r'([A-D])', raw_answer, re.IGNORECASE) # Thêm re.IGNORECASE để bắt cả chữ in thường
        if match_ans:
            dapan_dung = match_ans.group(1).upper()
        
        sql = f"INSERT INTO CauHoi (lop, hoc_ki, chuong, muc_do, noidung, dapan_a, dapan_b, dapan_c, dapan_d, dapan_dung) VALUES ({lop}, {hoc_ki}, '{chuong}', 1, '{noidung}', '{ans_a}', '{ans_b}', '{ans_c}', '{ans_d}', '{dapan_dung}');"
        sql_statements.append(sql)

if len(sql_statements) > 0:
    with open("cauhoi_final.sql", "w", encoding="utf-8") as f:
        for s in sql_statements:
            f.write(s + "\n")
    print(f"3. THÀNH CÔNG! Đã tạo được {len(sql_statements)} câu lệnh INSERT (kèm đáp án chuẩn) vào file 'cauhoi_final.sql'.")
else:
    print("3. THẤT BẠI: Không bóc tách được câu hỏi. Hãy kiểm tra lại format.")