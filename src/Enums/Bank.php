<?php

namespace Takashato\VietQr\Enums;

/**
 * Vietnamese bank BIN codes (NAPAS).
 *
 * @see https://www.sbv.gov.vn/ State Bank of Vietnam
 * @see https://vietqr.net/ VietQR Official
 */
enum Bank: string
{
    // State-owned commercial banks
    case VIETCOMBANK = '970436';
    case VIETINBANK = '970415';
    case BIDV = '970418';
    case AGRIBANK = '970405';

    // Joint-stock commercial banks
    case TECHCOMBANK = '970407';
    case MB_BANK = '970422';
    case ACB = '970416';
    case VPB = '970432';
    case SACOMBANK = '970403';
    case TPB = '970423';
    case HDBank = '970437';
    case VIB = '970441';
    case SHB = '970443';
    case SCB = '970429';
    case OCB = '970448';
    case MSB = '970426';
    case EXIMBANK = '970431';
    case SEABANK = '970440';
    case LPB = '970449';
    case ABBANK = '970425';
    case BAC_A_BANK = '970409';
    case PVCOMBANK = '970412';
    case NCBANK = '970419';
    case VietBank = '970433';
    case KIENLONGBANK = '970452';
    case NAM_A_BANK = '970428';
    case SAIGONBANK = '970400';
    case PG_BANK = '970430';
    case VIET_A_BANK = '970427';
    case BAOVIETBANK = '970438';
    case GPBANK = '970406';
    case CBBANK = '970444';
    case VNCB = '970454';
    case VietCapitalBank = '970456';
    case DONG_A_BANK = '970406';
    case OCEANBANK = '970414';

    // Foreign bank branches
    case HSBC = '458761';
    case Standard_Chartered = '970410';
    case Shinhan_Bank = '970424';
    case UOB = '970458';
    case Woori_Bank = '970457';
    case CIMB = '422589';
    case PublicBank = '970439';
    case ANZ = '970411';
    case Hong_Leong_Bank = '970442';
    case Indovina_Bank = '970434';
    case IBKHN = '970455';

    // Policy banks
    case VBSP = '970402';
    case VDB = '970404';

    // E-wallets & Fintech
    case VNPT_Money = '970461';
    case CAKE = '546034';
    case UBANK = '546035';
    case TIMO = '963388';
    case TNEX = '970462';
    case KBank = '668888';

    /**
     * Get the bank name in Vietnamese.
     */
    public function nameVi(): string
    {
        return match ($this) {
            // State-owned
            self::VIETCOMBANK => 'Ngân hàng TMCP Ngoại thương Việt Nam',
            self::VIETINBANK => 'Ngân hàng TMCP Công Thương Việt Nam',
            self::BIDV => 'Ngân hàng TMCP Đầu tư và Phát triển Việt Nam',
            self::AGRIBANK => 'Ngân hàng Nông nghiệp và Phát triển Nông thôn',

            // Joint-stock
            self::TECHCOMBANK => 'Ngân hàng TMCP Kỹ thương Việt Nam',
            self::MB_BANK => 'Ngân hàng TMCP Quân đội',
            self::ACB => 'Ngân hàng TMCP Á Châu',
            self::VPB => 'Ngân hàng TMCP Việt Nam Thịnh Vượng',
            self::SACOMBANK => 'Ngân hàng TMCP Sài Gòn Thương Tín',
            self::TPB => 'Ngân hàng TMCP Tiên Phong',
            self::HDBank => 'Ngân hàng TMCP Phát triển Thành phố Hồ Chí Minh',
            self::VIB => 'Ngân hàng TMCP Quốc tế Việt Nam',
            self::SHB => 'Ngân hàng TMCP Sài Gòn - Hà Nội',
            self::SCB => 'Ngân hàng TMCP Sài Gòn',
            self::OCB => 'Ngân hàng TMCP Phương Đông',
            self::MSB => 'Ngân hàng TMCP Hàng Hải Việt Nam',
            self::EXIMBANK => 'Ngân hàng TMCP Xuất Nhập Khẩu Việt Nam',
            self::SEABANK => 'Ngân hàng TMCP Đông Nam Á',
            self::LPB => 'Ngân hàng TMCP Bưu điện Liên Việt',
            self::ABBANK => 'Ngân hàng TMCP An Bình',
            self::BAC_A_BANK => 'Ngân hàng TMCP Bắc Á',
            self::PVCOMBANK => 'Ngân hàng TMCP Đại Chúng Việt Nam',
            self::NCBANK => 'Ngân hàng TMCP Quốc Dân',
            self::VietBank => 'Ngân hàng TMCP Việt Nam Thương Tín',
            self::KIENLONGBANK => 'Ngân hàng TMCP Kiên Long',
            self::NAM_A_BANK => 'Ngân hàng TMCP Nam Á',
            self::SAIGONBANK => 'Ngân hàng TMCP Sài Gòn Công Thương',
            self::PG_BANK => 'Ngân hàng TMCP Xăng dầu Petrolimex',
            self::VIET_A_BANK => 'Ngân hàng TMCP Việt Á',
            self::BAOVIETBANK => 'Ngân hàng TMCP Bảo Việt',
            self::GPBANK => 'Ngân hàng TMCP Dầu khí Toàn cầu',
            self::CBBANK => 'Ngân hàng TMCP Xây dựng Việt Nam',
            self::VNCB => 'Ngân hàng TMCP Việt Nam Công thương',
            self::VietCapitalBank => 'Ngân hàng TMCP Bản Việt',
            self::DONG_A_BANK => 'Ngân hàng TMCP Đông Á',
            self::OCEANBANK => 'Ngân hàng TMCP Đại Dương',

            // Foreign
            self::HSBC => 'Ngân hàng TNHH MTV HSBC Việt Nam',
            self::Standard_Chartered => 'Ngân hàng TNHH MTV Standard Chartered Việt Nam',
            self::Shinhan_Bank => 'Ngân hàng TNHH MTV Shinhan Việt Nam',
            self::UOB => 'Ngân hàng TNHH MTV UOB Việt Nam',
            self::Woori_Bank => 'Ngân hàng TNHH MTV Woori Việt Nam',
            self::CIMB => 'Ngân hàng TNHH MTV CIMB Việt Nam',
            self::PublicBank => 'Ngân hàng TNHH MTV Public Việt Nam',
            self::ANZ => 'Ngân hàng TNHH MTV ANZ Việt Nam',
            self::Hong_Leong_Bank => 'Ngân hàng TNHH MTV Hong Leong Việt Nam',
            self::Indovina_Bank => 'Ngân hàng TNHH Indovina',
            self::IBKHN => 'Ngân hàng Công nghiệp Hàn Quốc - CN Hà Nội',

            // Policy banks
            self::VBSP => 'Ngân hàng Chính sách Xã hội',
            self::VDB => 'Ngân hàng Phát triển Việt Nam',

            // E-wallets & Fintech
            self::VNPT_Money => 'VNPT Money',
            self::CAKE => 'CAKE by VPBank',
            self::UBANK => 'Ubank by VPBank',
            self::TIMO => 'Timo by Ban Viet',
            self::TNEX => 'TNEX by MSB',
            self::KBank => 'KBank - KASIKORNBANK',
        };
    }

    /**
     * Get the bank short name.
     */
    public function shortName(): string
    {
        return match ($this) {
            self::VIETCOMBANK => 'Vietcombank',
            self::VIETINBANK => 'VietinBank',
            self::BIDV => 'BIDV',
            self::AGRIBANK => 'Agribank',
            self::TECHCOMBANK => 'Techcombank',
            self::MB_BANK => 'MB Bank',
            self::ACB => 'ACB',
            self::VPB => 'VPBank',
            self::SACOMBANK => 'Sacombank',
            self::TPB => 'TPBank',
            self::HDBank => 'HDBank',
            self::VIB => 'VIB',
            self::SHB => 'SHB',
            self::SCB => 'SCB',
            self::OCB => 'OCB',
            self::MSB => 'MSB',
            self::EXIMBANK => 'Eximbank',
            self::SEABANK => 'SeABank',
            self::LPB => 'LienVietPostBank',
            self::ABBANK => 'ABBank',
            self::BAC_A_BANK => 'Bac A Bank',
            self::PVCOMBANK => 'PVcomBank',
            self::NCBANK => 'NCB',
            self::VietBank => 'VietBank',
            self::KIENLONGBANK => 'KienLongBank',
            self::NAM_A_BANK => 'Nam A Bank',
            self::SAIGONBANK => 'SaigonBank',
            self::PG_BANK => 'PGBank',
            self::VIET_A_BANK => 'VietABank',
            self::BAOVIETBANK => 'BaoVietBank',
            self::GPBANK => 'GPBank',
            self::CBBANK => 'CBBank',
            self::VNCB => 'VNCB',
            self::VietCapitalBank => 'Viet Capital Bank',
            self::DONG_A_BANK => 'DongABank',
            self::OCEANBANK => 'OceanBank',
            self::HSBC => 'HSBC',
            self::Standard_Chartered => 'Standard Chartered',
            self::Shinhan_Bank => 'Shinhan Bank',
            self::UOB => 'UOB',
            self::Woori_Bank => 'Woori Bank',
            self::CIMB => 'CIMB',
            self::PublicBank => 'Public Bank',
            self::ANZ => 'ANZ',
            self::Hong_Leong_Bank => 'Hong Leong Bank',
            self::Indovina_Bank => 'Indovina',
            self::IBKHN => 'IBK',
            self::VBSP => 'VBSP',
            self::VDB => 'VDB',
            self::VNPT_Money => 'VNPT Money',
            self::CAKE => 'CAKE',
            self::UBANK => 'Ubank',
            self::TIMO => 'Timo',
            self::TNEX => 'TNEX',
            self::KBank => 'KBank',
        };
    }

    /**
     * Get the bank BIN code (alias for value).
     */
    public function bin(): string
    {
        return $this->value;
    }

    /**
     * Find bank by BIN code.
     */
    public static function fromBin(string $bin): ?self
    {
        foreach (self::cases() as $bank) {
            if ($bank->value === $bin) {
                return $bank;
            }
        }

        return null;
    }

    /**
     * Search banks by name (case-insensitive).
     *
     * @return array<self>
     */
    public static function search(string $query): array
    {
        $query = mb_strtolower($query);
        $results = [];

        foreach (self::cases() as $bank) {
            if (
                str_contains(mb_strtolower($bank->shortName()), $query) ||
                str_contains(mb_strtolower($bank->nameVi()), $query) ||
                str_contains(mb_strtolower($bank->name), $query)
            ) {
                $results[] = $bank;
            }
        }

        return $results;
    }
}