/**
 *
 * Login
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import { selectBuildingCluster } from "../../redux/selectors";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectLogin from "./selectors";

import { Col, Row } from "antd";

import { Page } from "components";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { defaultAction } from "./actions";

export class PrivacyPolicy extends React.PureComponent {
  state = {
    loginLoading: false,
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  render() {
    return (
      <Page inner>
        <Row type="flex" align="middle">
          <Col span={24} style={{ display: "grid" }}>
            <Row style={{ textAlign: "center" }}>
              <strong style={{ fontSize: 18 }}>DATA PRIVACY TERMS/</strong>
              <strong
                style={{
                  fontSize: 18,
                  fontStyle: "italic",
                  color: "rgb(183, 183, 183)",
                }}
              >
                ĐIỀU KHOẢN BẢO MẬT THÔNG TIN
              </strong>
            </Row>
            <Row>
              <strong
                style={{
                  fontSize: 16,
                }}
              >
                1. General terms/
              </strong>
              <strong
                style={{
                  fontSize: 16,
                  fontStyle: "italic",
                  color: "rgb(183, 183, 183)",
                }}
              >
                Điều khoản chung
              </strong>
            </Row>
            <span style={{ paddingLeft: 16, paddingTop: 8 }}>
              THESE TERMS AND CONDITIONS (hereinafter referred to as “Terms”)
              shall govern rights and obligations of users relating to the use
              of this software application (hereinafter referred to as “App”)
              provided by <strong>VIETNAM GS INDUSTRY ONE-MEMBER LLC</strong>, a
              company established and operating under the laws of Vietnam, and
              having enterprise code number 0304986867 (hereinafter referred to
              as “we”, “us”, or “our”).
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              CÁC ĐIỀU KHOẢN VÀ ĐIỀU KIỆN NÀY (sau đây gọi là “Điều Khoản”) sẽ
              điều chỉnh các quyền và nghĩa vụ của người dùng liên quan đến việc
              sử dụng ứng dụng phần mềm này (sau đây gọi là “Ứng Dụng”) được
              cung cấp bởi
              <strong> CÔNG TY TNHH MỘT THÀNH VIÊN GS INDUSTRY VIỆT NAM</strong>
              , một công ty được thành lập và hoạt động theo pháp luật Việt Nam,
              có mã số doanh nghiệp 0304986867 (sau đây gọi là “chúng tôi”, hoặc
              “của chúng tôi”).
            </span>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              By downloading, installing, accessing, using and/or updating the
              App, the persons downloading, installing, accessing, using and/or
              updating the App (hereinafter referred to as “you” or “your”)
              agree that you have entirely read, understood and agreed to these
              Terms, and accepted that any of your activities relating to the
              App, including without limitation to your rights and obligations
              of the use of the App and the Services (as defined below), shall
              be governed by these Terms.
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Bằng cách tải xuống, cài đặt, truy cập, sử dụng và/hoặc cập nhật
              Ứng Dụng, những người tải xuống, cài đặt, truy cập, sử dụng
              và/hoặc cập nhật Ứng Dụng (sau đây gọi là “bạn” hoặc “của bạn”)
              đồng ý rằng bạn đã đọc, hiểu và đồng ý hoàn toàn với các Điều
              Khoản này và chấp nhận rằng bất kỳ hoạt động nào của bạn liên quan
              với Ứng Dụng, bao gồm nhưng không giới hạn ở các quyền và nghĩa vụ
              của bạn khi sử dụng Ứng Dụng và Dịch Vụ (như được định nghĩa bên
              dưới), sẽ chịu sự điều chỉnh của các Điều Khoản này.
            </span>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              If you do not agree with any of these Terms (or any updated
              version from time to time), you have the sole discretion over not
              downloading, not installing, not using the App or over deleting
              your account in the App, removing the App from your mobile device.
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Nếu bạn không đồng ý với bất kỳ điều khoản nào trong các Điều
              Khoản này (hoặc bất kỳ phiên bản cập nhật nào tại từng thời điểm),
              bạn có toàn quyền quyết định không tải xuống, không cài đặt, không
              sử dụng Ứng Dụng hoặc xóa tài khoản của bạn trên Ứng Dụng, xóa Ứng
              Dụng khỏi thiết bị di động của bạn.
            </span>
            <Row style={{ paddingTop: 16 }}>
              <strong
                style={{
                  fontSize: 16,
                }}
              >
                2. Definitions/
              </strong>
              <strong
                style={{
                  fontSize: 16,
                  fontStyle: "italic",
                  color: "rgb(183, 183, 183)",
                }}
              >
                Định nghĩa
              </strong>
            </Row>
            <span style={{ paddingLeft: 16, paddingTop: 8 }}>
              “<strong>Personal Data</strong>” is information in the form of
              symbols, letters, numbers, images, sounds, or equivalences in the
              electronic environment associated with a particular individual or
              used to identify a particular individual, including the Required
              Personal Data, and other personal data as stipulated under the
              law.
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              “<strong>Dữ Liệu Cá Nhân</strong>” là thông tin dưới dạng ký hiệu,
              chữ viết, chữ số, hình ảnh, âm thanh hoặc dạng tương tự trên môi
              trường điện tử gắn liền với một cá nhân cụ thể hoặc giúp xác định
              một cá nhân cụ thể, bao gồm Dữ Liệu Cá Nhân Bắt Buộc, và dữ liệu
              cá nhân khác theo quy định của pháp luật.
            </span>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              “<strong>Required Personal Data</strong>” is the Personal Data
              that is required to be processed so that we can provide the
              Services to you in the App, including the Sensitive Personal Data
              and other Personal Data as provided in Article 4 of these Terms.
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              “<strong>Dữ Liệu Cá Nhân Bắt Buộc</strong>” là Dữ Liệu Cá Nhân cần
              thiết phải được xử lý để chúng tôi có thể cung cấp Dịch Vụ cho bạn
              trong Ứng Dụng, bao gồm Dữ Liệu Cá Nhân Nhạy Cảm và Dữ Liệu Cá
              Nhân khác được quy định tại Điều 4 của Điều Khoản này.
            </span>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              “<strong>Sensitive Personal Data</strong>” is the Personal Data
              that is associated with the privacy of individuals, and if being
              infringed, it shall directly affect an individual's legal rights
              and interests, as provided in detail in Article 4 of these Terms.
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              “<strong>Dữ Liệu Cá Nhân Nhạy Cảm</strong>” là Dữ Liệu Cá Nhân gắn
              liền với quyền riêng tư của cá nhân mà khi bị xâm phạm sẽ gây ảnh
              hưởng trực tiếp tới quyền và lợi ích hợp pháp của cá nhân, như
              được quy định chi tiết tại Điều 4 của Điều Khoản này.
            </span>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              “<strong>Data Processing</strong>” is one or several activities
              that impact on the Personal Data, including collection, recording,
              analysis, confirmation, storage, rectification, disclosure,
              consolidation, access, traceability, retrieval, encryption,
              decryption, copying, sharing, transmission, provision, transfer,
              deletion, destruction of the Personal Data or other relevant
              activities. For clarification, any term
              <strong> “processing”, “processed”, or “process”</strong>{" "}
              mentioned in these Terms shall be the Data Processing.
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              “<strong>Xử Lý Dữ Liệu</strong>” là một hoặc nhiều hoạt động tác
              động tới Dữ Liệu Cá Nhân, bao gồm thu thập, ghi, phân tích, xác
              nhận, lưu trữ, chỉnh sửa, công khai, kết hợp, truy cập, truy xuất,
              thu hồi, mã hóa, giải mã, sao chép, chia sẻ, truyền đưa, cung cấp,
              chuyển giao, xóa, hủy Dữ Liệu Cá Nhân hoặc các hành động khác có
              liên quan. Để làm rõ, bất kỳ thuật ngữ
              <strong> thực hiện xử lý, được xử lý, hoặc xử lý</strong> nào được
              đề cập trong các Điều Khoản này đều nghĩa là Xử Lý Dữ Liệu.
            </span>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              “<strong>Project</strong>” is a residential housing project of our
              real estate projects that is corresponding to your residence,
              particularly GS Metrocity Nha Be, Thu Thiem and Long Binh, as the
              case may be.
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              “<strong>Dự án</strong>” là một dự án nhà ở trong các dự án bất
              động sản của chúng tôi tương ứng với nơi cư trú của bạn, cụ thể là
              Nha Be Metrocity GS, Thủ Thiêm và Long Bình, tùy từng trường hợp.
            </span>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              “<strong>Services</strong>” is any services provided in the App,
              including:
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              “<strong>Dịch Vụ</strong>” là bất kỳ dịch vụ nào được cung cấp
              trong Ứng Dụng, bao gồm:
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              • To be provided with updates about the news of the applicable
              Project and building, be provided with general notification and
              essential announcements from the Management Board and from us;
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Được cập nhật tin tức về Dự án và tòa nhà liên quan, được cung cấp
              thông báo chung và thông báo quan trọng từ Ban Quản Lý và từ chúng
              tôi;
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              • To be entitled to request for immediate responses and urgent
              support from the Management Board and from us;
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Được yêu cầu các phản hồi ngay lập tức và hỗ trợ khẩn cấp từ Ban
              Quản Lý và từ chúng tôi;
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              • To be entitled to use a new mechanism of paying fees relating to
              services provided at the applicable Project, including without
              limitation to management fees, parking fees and amenities fees;
              and
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Được sử dụng một cơ chế mới để thanh toán các phí liên quan đến
              các dịch vụ được cung cấp tại Dự án liên quan, bao gồm nhưng không
              giới hạn ở phí quản lý, phí đỗ xe và phí tiện nghi; và
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              • To be entitled to make booking for amenities provided at the
              applicable Project.
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Được đặt chỗ cho các tiện ích tại Dự án liên quan.
            </span>
            <Row style={{ paddingTop: 16 }}>
              <strong
                style={{
                  fontSize: 16,
                }}
              >
                3. Purposes of processing your Personal Data and Functions of
                the App/
              </strong>
              <strong
                style={{
                  fontSize: 16,
                  fontStyle: "italic",
                  color: "rgb(183, 183, 183)",
                }}
              >
                Mục đích xử lý Dữ Liệu Cá Nhân của bạn và chức năng của Ứng Dụng
              </strong>
            </Row>
            <span style={{ paddingLeft: 16, paddingTop: 8 }}>
              We shall collect and process your Personal Data that you have
              provided to us in the App for the purposes of providing the
              Services to you and facilitating your usage of the Services, i.e.,
              the use of the App functions. You can use the App with the
              following functions:
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Chúng tôi sẽ thu thập và xử lý Dữ Liệu Cá Nhân của bạn mà bạn đã
              cung cấp cho chúng tôi trong Ứng Dụng nhằm các mục đích cung cấp
              Dịch Vụ cho bạn và tạo điều kiện thuận lợi cho việc bạn sử dụng
              Dịch Vụ (tức là việc sử dụng các chức năng của Ứng Dụng). Bạn có
              thể sử dụng Ứng Dụng với các chức năng sau:
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              • Receiving updates about the news of the applicable Project and
              building, be provided with general notification and essential
              announcements from the Management Board and from us;
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Nhận cập nhật tin tức về Dự án và tòa nhà liên quan, được cung cấp
              thông báo chung và thông báo quan trọng từ Ban Quản Lý và từ chúng
              tôi;
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              • Requesting for immediate responses and urgent support from the
              Management Board and from us;
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Có yêu cầu các phản hồi ngay lập tức và hỗ trợ khẩn cấp từ Ban
              Quản Lý và từ chúng tôi;
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              • To use a new mechanism of paying fees relating to services
              provided at the applicable Project, including without limitation
              to management fees, parking fees and amenities fees; and
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Sử dụng một cơ chế mới để thanh toán các phí liên quan đến các
              dịch vụ được cung cấp tại Dự án liên quan, bao gồm nhưng không
              giới hạn ở phí quản lý, phí đỗ xe và phí tiện nghi; và
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              • To make booking for amenities provided at the applicable
              Project.
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Đặt chỗ cho các tiện ích tại Dự án liên quan.
            </span>
            <Row style={{ paddingTop: 16 }}>
              <strong
                style={{
                  fontSize: 16,
                }}
              >
                4. Collecting and processing your Personal Data/
              </strong>
              <strong
                style={{
                  fontSize: 16,
                  fontStyle: "italic",
                  color: "rgb(183, 183, 183)",
                }}
              >
                Thu thập và xử lý Dữ Liệu Cá Nhân của bạn
              </strong>
            </Row>
            <span style={{ paddingLeft: 16, paddingTop: 8 }}>
              For the appropriate use of the App functions, we shall collect and
              process your Personal Data during your usage of the App and only
              in the App, including the Required Personal Data as follows:
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Vì mục đích sử dụng các chức năng của Ứng Dụng, chúng tôi sẽ thu
              thập và xử lý Dữ Liệu Cá Nhân của bạn trong quá trình bạn sử dụng
              Ứng Dụng và chỉ trong Ứng Dụng, bao gồm Dữ Liệu Cá Nhân Bắt Buộc
              sau:
            </span>
            <Row style={{ paddingLeft: 32, paddingTop: 16 }}>
              <strong style={{}}>Sensitive Personal Data/</strong>
              <strong
                style={{
                  fontStyle: "italic",
                  color: "rgb(183, 183, 183)",
                }}
              >
                Dữ Liệu Cá Nhân Nhạy Cảm
              </strong>
            </Row>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>
              • Information about racial or ethnic origin
            </span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Thông tin liên quan đến nguồn gốc chủng tộc, nguồn gốc dân tộc
            </span>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>
              • Other Personal Data as provided under the law that requires
              vital protection.
            </span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Dữ Liệu Cá Nhân khác được pháp luật quy định là đặc thù và cần có
              biện pháp bảo mật cần thiết.
            </span>
            <Row style={{ paddingLeft: 32, paddingTop: 16 }}>
              <strong style={{}}>Other Personal Data/</strong>
              <strong
                style={{
                  fontStyle: "italic",
                  color: "rgb(183, 183, 183)",
                }}
              >
                Dữ Liệu Cá Nhân khác
              </strong>
            </Row>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>• Full name</span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Tên đầy đủ
            </span>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>
              • Date of birth
            </span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Ngày, tháng, năm sinh
            </span>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>• Gender</span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Giới tính
            </span>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>
              • Place of birth, registered place of birth; place of permanent
              residence; place of temporary residence; current place of
              residence; hometown; contact address
            </span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Nơi sinh, nơi đăng ký khai sinh, nơi thường trú, nơi tạm trú, nơi
              ở hiện tại, quê quán, địa chỉ liên hệ
            </span>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>
              • Nationality
            </span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Quốc tịch
            </span>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>
              • Profile photo (if any)
            </span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Hình ảnh (nếu có)
            </span>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>
              • Phone number; ID Card number, personal identification number,
              passport number, driver’s license number, license plate, taxpayer
              identification number, social security number and health insurance
              card number
            </span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Số điện thoại, số chứng minh nhân dân, số định danh cá nhân, số hộ
              chiếu, số giấy phép lái xe, số biển số xe, số mã số thuế cá nhân,
              số bảo hiểm xã hội, số thẻ bảo hiểm y tế
            </span>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>
              • Marital status
            </span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Tình trạng hôn nhân
            </span>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>
              • Information about the individual’s family
            </span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Thông tin về mối quan hệ gia đình
            </span>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>
              • Digital account information; personal data that reflects
              activities and activity history in App.
            </span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Thông tin về tài khoản số của cá nhân; dữ liệu cá nhân phản ánh
              hoạt động, lịch sử hoạt động trên Ứng Dụng.
            </span>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              For clarification, if you are unable to provide any of the the
              Required Personal Data, you cannot use the Services and, or the
              App.
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Để làm rõ, nếu bạn không thể cung cấp bất kỳ Dữ Liệu Cá Nhân Bắt
              Buộc nào, bạn không thể sử dụng Dịch Vụ và, hoặc Ứng Dụng.
            </span>
            <Row style={{ paddingTop: 16 }}>
              <strong
                style={{
                  fontSize: 16,
                }}
              >
                5. Scope of your Personal Data processing/
              </strong>
              <strong
                style={{
                  fontSize: 16,
                  fontStyle: "italic",
                  color: "rgb(183, 183, 183)",
                }}
              >
                Phạm vi xử lý Dữ Liệu Cá Nhân của bạn
              </strong>
            </Row>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              For the appropriate use of the App functions, we have to collect
              and process your Personal Data and disclose your Personal Data to
              the third parties for its processing of your Personal Data. To do
              this, we shall work with the following third parties:
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Vì mục đích sử dụng các chức năng của Ứng Dụng, chúng tôi phải thu
              thập và xử lý Dữ Liệu Cá Nhân của bạn và tiết lộ Dữ Liệu Cá Nhân
              của bạn cho bên thứ ba để họ xử lý Dữ Liệu Cá Nhân của bạn. Để làm
              điều này, chúng tôi sẽ làm việc với các bên thứ ba sau:
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              - Payment Service providers
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Nhà cung cấp dịch vụ thanh toán
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              - Real estate property management agency(ies) of your residential
              project
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              (Các) Công ty quản lý bất động sản của dự án khu dân cư của bạn
            </span>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              The processing of your Personal Data shall be commenced at the
              time you register an account on the App and shall be ended at upon
              your deletion of your account on the App. Notwithstanding the end
              of the processing of your Personal Data, your Personal Data shall
              be stored until we are not entitled to have stored your Personal
              Data on account of legal requirements under the applicable law and
              your request for deletion to your Personal Data in accordance with
              the law. In case you have a request for deletion to your Personal
              Data in the App in accordance with the law, your request shall be
              initially implemented within 72 hours after we have received your
              request in accordance with Article 6 of these Terms.
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Việc xử lý Dữ Liệu Cá Nhân của bạn sẽ được bắt đầu vào thời điểm
              bạn đăng ký tài khoản trên Ứng Dụng và sẽ kết thúc khi bạn xóa tài
              khoản của bạn trên Ứng Dụng. Mặc dù việc xử lý Dữ Liệu Cá Nhân của
              bạn đã kết thúc, Dữ Liệu Cá Nhân của bạn vẫn sẽ được lưu trữ cho
              đến khi chúng tôi không còn quyền lưu giữ Dữ Liệu Cá Nhân của bạn
              do yêu cầu pháp lý theo luật hiện hành và bạn yêu cầu xóa Dữ Liệu
              Cá Nhân của bạn theo quy định của pháp luật. Trong trường hợp bạn
              có yêu cầu xóa Dữ Liệu Cá Nhân của bạn trên Ứng Dụng theo quy định
              của pháp luật, yêu cầu của bạn sẽ bắt đầu được thực hiện trong
              vòng 72 giờ sau khi chúng tôi nhận được yêu cầu của bạn theo Điều
              6 của các Điều Khoản này.
            </span>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              The processing of your Personal Data shall be conducted as
              follows:
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Việc xử lý Dữ Liệu Cá Nhân của bạn sẽ được thực hiện như sau:
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              1. You shall input your Personal Data into the app
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Bạn sẽ điền Dữ Liệu Cá Nhân của bạn vào ứng dụng
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              2. The Management Board shall receive your Personal Data input
              through the app and process the information to provide services to
              you.
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Ban Quản Lý sẽ tiếp nhận Dữ Liệu Cá Nhân của bạn nhập vào qua ứng
              dụng và xử lý những thông tin đó để cung cấp dịch vụ cho bạn.
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              3. Processed data is store in the app server.
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Thông tin xử lý được lưu trữ trên server của app.
            </span>
            <Row style={{ paddingTop: 16 }}>
              <strong
                style={{
                  fontSize: 16,
                }}
              >
                6. Your Rights/
              </strong>
              <strong
                style={{
                  fontSize: 16,
                  fontStyle: "italic",
                  color: "rgb(183, 183, 183)",
                }}
              >
                Quyền của bạn
              </strong>
            </Row>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              As for collecting and processing your Personal Data, unless
              otherwise provided for under the law, you have the rights with
              respect to your Personal Data as follows:
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Đối với việc thu thập và xử lý Dữ Liệu Cá Nhân của bạn, trừ khi
              luật có quy định khác, bạn có các quyền đối với Dữ Liệu Cá Nhân
              của bạn như sau:
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              1. Rights to be informed
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Quyền được thông báo
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              You have the rights to be informed of your Personal Data
              processing activities.
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Bạn có quyền được thông báo về các hoạt động xử lý Dữ Liệu Cá Nhân
              của bạn.
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              2. Rights to give consent
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Quyền đồng ý
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              You have the rights to give your consent to the processing of your
              Personal Data.
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Bạn có quyền đồng ý với việc xử lý Dữ Liệu Cá Nhân của bạn.
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              3. Rights to access your Personal Data
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Quyền truy cập Dữ Liệu Cá Nhân của bạn
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              You have the rights to access your Personal Data in order to view,
              correct or request correction of your Personal Data.
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Bạn có quyền truy cập Dữ Liệu Cá Nhân của bạn để xem, sửa hoặc yêu
              cầu sửa Dữ Liệu Cá Nhân của bạn.
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              4. Rights to withdraw consent
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Quyền rút lại sự đồng ý
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              You have the rights to withdraw your consent that you provided. In
              case you withdraw your consent that you provided, you cannot use
              the Services and, or the App.
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Bạn có quyền rút lại sự đồng ý mà bạn đã cung cấp. Trong trường
              hợp bạn rút lại sự đồng ý mà bạn đã cung cấp, bạn không thể sử
              dụng Dịch Vụ và hoặc Ứng Dụng.
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              5. Rights to delete your Personal Data
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Quyền xóa Dữ Liệu Cá Nhân của bạn
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              You have the rights to delete or request deletion of your Personal
              Data in accordance with the law. In case you delete or request
              deletion of your Personal Data that you provided, you cannot use
              the Services and, or the App.
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Bạn có quyền xóa hoặc yêu cầu xóa Dữ Liệu Cá Nhân của bạn theo quy
              định của pháp luật. Trong trường hợp bạn xóa hoặc yêu cầu xóa Dữ
              liệu cá nhân mà bạn đã cung cấp, bạn không thể sử dụng Dịch Vụ và
              hoặc Ứng Dụng.
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              6. Rights to restrict the processing of your Personal Data
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Quyền hạn chế xử lý Dữ Liệu Cá Nhân của bạn
            </span>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>
              You have the rights to restrict the processing of your Personal
              Data. In case you restrict the processing of your Required
              Personal Data, you cannot use the Services and, or the App.
            </span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Bạn có quyền hạn chế việc xử lý Dữ Liệu Cá Nhân của bạn. Trong
              trường hợp bạn giới hạn việc xử lý Dữ Liệu Cá Nhân Bắt Buộc, bạn
              không thể sử dụng Dịch Vụ và/hoặc Ứng Dụng.
            </span>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>
              The restriction on the processing of your Personal Data shall be
              initially implemented within 72 hours after we have received your
              request, and applied to all of the Personal Data that (a) we have
              obtained from you and (b) you have requested to restrict on.
            </span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Việc hạn chế xử lý Dữ Liệu Cá Nhân của bạn sẽ bắt đầu được thực
              hiện trong vòng 72 giờ sau khi chúng tôi nhận được yêu cầu của bạn
              và áp dụng cho tất cả Dữ Liệu Cá Nhân mà (a) chúng tôi có được từ
              bạn và (b) bạn đã yêu cầu giới hạn.
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              7. Rights to be provided with your Personal Data
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Quyền được cung cấp Dữ Liệu Cá Nhân của bạn
            </span>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>
              You have the rights to request us to provide you with your
              Personal Data.
            </span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Bạn có quyền yêu cầu chúng tôi cung cấp cho bạn Dữ Liệu Cá Nhân
              của bạn.
            </span>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>
              We shall initiate implementing your request within 72 hours after
              we have received your request.
            </span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Chúng tôi sẽ bắt đầu thực hiện yêu cầu của bạn trong vòng 72 giờ
              sau khi chúng tôi nhận được yêu cầu của bạn.
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              8. Rights to object to processing
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Quyền phản đối xử lý
            </span>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>
              You have the rights to object to the processing of your Personal
              Data in order to prevent or restrict the disclosure of your
              Personal Data or the use of your Personal Data for advertising and
              marketing purposes. In case you object to the processing of your
              Required Personal Data, prevent or restrict the disclosure of your
              Required Personal Data, you cannot use the Services and, or the
              App.
            </span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Bạn có quyền phản đối việc xử lý Dữ Liệu Cá Nhân của bạn để ngăn
              chặn hoặc hạn chế việc tiết lộ Dữ Liệu Cá Nhân của bạn hoặc việc
              sử dụng Dữ Liệu Cá Nhân của bạn cho mục đích quảng cáo và tiếp
              thị. Trong trường hợp bạn phản đối việc xử lý Dữ Liệu Cá Nhân Bắt
              Buộc, ngăn chặn hoặc hạn chế việc tiết lộ Dữ Liệu Cá Nhân Bắt
              Buộc, bạn không thể sử dụng Dịch Vụ và, hoặc Ứng Dụng.
            </span>
            <span style={{ paddingLeft: 48, paddingTop: 16 }}>
              We shall initiate implementing your request within 72 hours after
              we have received your request.
            </span>
            <span
              style={{
                paddingLeft: 48,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Chúng tôi sẽ bắt đầu thực hiện yêu cầu của bạn trong vòng 72 giờ
              sau khi chúng tôi nhận được yêu cầu của bạn.
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              9. You shall have only one user account to use functions of the
              App. In case you lost your device that you have downloaded and
              installed the App, you are responsible for informing us
              immediately.
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Bạn chỉ có một tài khoản người dùng để sử dụng các chức năng của
              Ứng Dụng. Trong trường hợp bạn bị mất thiết bị đã tải và cài đặt
              Ứng Dụng, bạn có trách nhiệm thông báo ngay cho chúng tôi.
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              10. We shall endeavour to adopt measures for preserving your
              Personal Data in accordance with the law and you have the rights
              to submit your queries, claims and requests relating to your
              Personal Data to us in accordance with provisions of These Terms.
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Chúng tôi sẽ nỗ lực áp dụng các biện pháp để bảo vệ Dữ Liệu Cá
              Nhân của bạn theo quy định của pháp luật và bạn có quyền gửi các
              câu hỏi, yêu cầu và khiếu nại của bạn liên quan đến Dữ liệu Cá
              nhân của bạn cho chúng tôi theo các quy định của Điều Khoản này.
            </span>
            <Row style={{ paddingTop: 16 }}>
              <strong
                style={{
                  fontSize: 16,
                }}
              >
                7. Your Obligations/
              </strong>
              <strong
                style={{
                  fontSize: 16,
                  fontStyle: "italic",
                  color: "rgb(183, 183, 183)",
                }}
              >
                Nghĩa vụ của bạn
              </strong>
            </Row>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              You shall have the obligations with respect to your Personal Data
              as follows:
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Bạn có nghĩa vụ đối với Dữ Liệu Cá Nhân của bạn như sau:
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              1. The Personal Data that you agree to provide us with your
              consent to the processing and disclosing of the Personal Data
              under these Terms has to be true, correct, complete at the time
              that you provide your Personal Data. You are responsible for
              amending your Personal Data, requesting us to amend your Personal
              Data, if your Personal Data changes. For further clarification,
              you shall be solely responsible for any misleading information
              resulting in restriction and effect on your rights, including your
              rights to your Personal Data.
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Dữ Liệu Cá Nhân mà bạn đồng ý cung cấp cho chúng tôi đối với việc
              xử lý và tiết lộ Dữ Liệu Cá Nhân theo các Điều Khoản này phải
              đúng, chính xác, đầy đủ tại thời điểm bạn cung cấp Dữ Liệu Cá Nhân
              của bạn. Bạn có trách nhiệm sửa đổi Dữ Liệu Cá Nhân của bạn, yêu
              cầu chúng tôi sửa đổi Dữ Liệu Cá Nhân của bạn, nếu Dữ Liệu Cá Nhân
              của bạn thay đổi. Để làm rõ thêm, bạn sẽ tự chịu trách nhiệm về
              bất kỳ thông tin sai lệch nào dẫn đến hạn chế và ảnh hưởng đến các
              quyền của bạn, bao gồm cả quyền đối với Dữ Liệu Cá Nhân của bạn.
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              2. You have to at all times preserve your Personal Data privacy,
              including without limitation to preserving the privacy of your
              accounts, passwords and other relevant details of your accounts
              that you have made in the App. It is your responsibility to not
              disclose any details of your accounts to any persons. We shall not
              accept any responsibilities arising from or relating to the
              disclosure of your Personal Data if you fail to comply with this
              term.
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Bạn phải luôn bảo vệ quyền riêng tư Dữ Liệu Cá Nhân của bạn, bao
              gồm nhưng không giới hạn việc bảo vệ quyền riêng tư của tài khoản,
              mật khẩu và các chi tiết liên quan khác của tài khoản mà bạn đã
              tạo trong Ứng Dụng. Bạn có trách nhiệm không tiết lộ bất kỳ chi
              tiết nào về tài khoản của bạn cho bất kỳ người nào. Chúng tôi sẽ
              không chấp nhận bất kỳ trách nhiệm nào phát sinh từ hoặc liên quan
              đến việc tiết lộ Dữ Liệu Cá Nhân của bạn nếu bạn không tuân thủ
              điều khoản này.
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              3. You have to at all times respect and preserve others’ Personal
              Data privacy, including without limitation to phishing, stealing,
              destroying, disclosing, and any other acts causing the leak of the
              Personal Data. We shall not accept any responsibilities arising
              from or relating to the disclosure of your Personal Data if you
              fail to comply with this term. For further clarification, you
              shall be solely responsible for any damage arising from or
              relating to your failure to comply with this term.
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Bạn phải luôn tôn trọng và bảo vệ quyền riêng tư Dữ Liệu Cá Nhân
              của người khác, bao gồm nhưng không giới hạn ở hành vi lừa đảo,
              đánh cắp, hủy hoại, tiết lộ và bất kỳ hành vi nào khác gây rò rỉ
              Dữ Liệu Cá Nhân. Chúng tôi sẽ không chấp nhận bất kỳ trách nhiệm
              nào phát sinh từ hoặc liên quan đến việc tiết lộ Dữ Liệu Cá Nhân
              của bạn nếu bạn không tuân thủ điều khoản này. Để làm rõ thêm, bạn
              sẽ tự chịu trách nhiệm về bất kỳ thiệt hại nào phát sinh từ hoặc
              liên quan đến việc bạn vi phạm điều khoản này.
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              4. You further have to warrant that at all times you are entitled
              to use the Services. You undertake that you use the App only for
              the purposes of using the Services. You shall not abuse or use the
              App for fraudulent purposes or to cause any inconvenience to
              others or to make fake transactions.
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Ngoài ra, bạn phải đảm bảo rằng bạn luôn có quyền sử dụng Dịch Vụ.
              Bạn cam kết rằng bạn chỉ sử dụng Ứng Dụng cho mục đích sử dụng
              Dịch Vụ. Bạn không được lạm dụng hoặc sử dụng Ứng Dụng cho các mục
              đích lừa đảo hoặc gây bất tiện cho người khác hoặc thực hiện các
              giao dịch giả mạo.
            </span>
            <span style={{ paddingLeft: 32, paddingTop: 16 }}>
              5. You have to at all times comply with law and regulations on
              personal data privacy.
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Bạn phải luôn tuân thủ pháp luật và các quy định về bảo mật dữ
              liệu cá nhân.
            </span>
            <Row style={{ paddingTop: 16 }}>
              <strong
                style={{
                  fontSize: 16,
                }}
              >
                8. Data of children/
              </strong>
              <strong
                style={{
                  fontSize: 16,
                  fontStyle: "italic",
                  color: "rgb(183, 183, 183)",
                }}
              >
                Dữ liệu trẻ em
              </strong>
            </Row>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              The collection, process and disclosure of children’s Personal Data
              shall be conducted in accordance with the law. The processing of
              children’s Personal Data shall be commenced only after we have
              that child’s consent if that child is 7 years old or above and the
              consent of that child’s guardian(s), except for cases provided
              under the law.
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Việc thu thập, xử lý và tiết lộ Dữ Liệu Cá Nhân của trẻ em sẽ được
              thực hiện theo quy định của pháp luật. Việc xử lý Dữ Liệu Cá Nhân
              của trẻ em sẽ chỉ được bắt đầu sau khi chúng tôi có sự đồng ý của
              đứa trẻ đó nếu đứa trẻ đó từ 7 tuổi trở lên và sự đồng ý của
              (những) người giám hộ của đứa trẻ đó, ngoại trừ các trường hợp
              được quy định theo luật.
            </span>
            <Row style={{ paddingTop: 16 }}>
              <strong
                style={{
                  fontSize: 16,
                }}
              >
                9. Miscellaneous/
              </strong>
              <strong
                style={{
                  fontSize: 16,
                  fontStyle: "italic",
                  color: "rgb(183, 183, 183)",
                }}
              >
                Điều khoản khác
              </strong>
            </Row>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              1. These Terms are governed by the laws of Vietnam. These Terms
              are legally binding on and enforceable between you and us. You may
              not transfer or assign your rights under these Terms, without our
              prior written approval.
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Các Điều Khoản này được điều chỉnh bởi luật pháp Việt Nam. Các
              Điều Khoản này ràng buộc về mặt pháp lý và có hiệu lực thi hành
              giữa bạn và chúng tôi. Bạn không được chuyển nhượng hoặc chuyển
              nhượng các quyền của bạn theo các Điều Khoản này mà không có sự
              chấp thuận trước bằng văn bản của chúng tôi.
            </span>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              2. Any dispute arising out of or in connection with these Terms
              shall be firstly settled by negotiation and conciliation between
              you and us within 30 days from the occurrence time of disputes.
              Should an amicable settlement be unfeasible, the dispute shall be
              finally settled by the competent court of Vietnam.
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Mọi tranh chấp phát sinh từ hoặc liên quan đến các Điều Khoản này
              trước tiên sẽ được giải quyết thông qua thương lượng và hòa giải
              giữa bạn và chúng tôi trong vòng 30 ngày kể từ thời điểm xảy ra
              tranh chấp. Nếu hòa giải không khả thi, tranh chấp sẽ được giải
              quyết cuối cùng bởi tòa án có thẩm quyền của Việt Nam.
            </span>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              3. These Terms are made into English and Vietnamese with equal
              validity. If there is a discrepancy between the English and
              Vietnamese versions, the English version shall be the prevailing
              version for the interpretation of these Terms.
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Các Điều Khoản này được lập thành tiếng Anh và tiếng Việt có giá
              trị như nhau. Nếu có sự khác biệt giữa phiên bản tiếng Anh và
              tiếng Việt, thì phiên bản tiếng Anh sẽ là phiên bản được sử dụng
              phổ biến để giải thích các Điều Khoản này.
            </span>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              4. If any provision under these Terms is considered as illegal,
              invalid or unenforceable in whole or in part, such provision shall
              not affect the validity of the rest of these Terms. We shall
              change the illegal, invalid or unenforceable provision in
              compliance with the law to the fullest extent that such provision
              is legal, valid and enforceable.
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Nếu bất kỳ điều khoản nào trong các Điều Khoản này được coi là bất
              hợp pháp, không hợp lệ hoặc không thể thi hành toàn bộ hoặc một
              phần, điều khoản đó sẽ không ảnh hưởng đến hiệu lực của phần còn
              lại của các Điều Khoản này. Chúng tôi sẽ thay đổi điều khoản bất
              hợp pháp, không hợp lệ hoặc không thể thực thi để tuân thủ luật
              pháp trong phạm vi tối đa để điều khoản đó hợp pháp, hợp lệ và có
              thể thi hành.
            </span>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              5. These Terms shall be effective from the day it is uploaded to a
              public platform for downloading. These Terms shall be subject to
              our changes, supplements and replacements from time to time and in
              case any of these Terms is changed, supplemented and replaced, the
              new terms shall be notified to you 30 Days in advance. These Terms
              are issued on August 1st, 2023.
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Các Điều Khoản này sẽ có hiệu lực kể từ ngày được tải lên một nền
              tảng công cộng để tải xuống. Các Điều Khoản này có thể được chúng
              tôi thay đổi, bổ sung và thay thế theo từng thời điểm và trong
              trường hợp bất kỳ khoản nào của Điều Khoản được thay đổi, bổ sung
              và thay thế, các điều khoản mới sẽ được thông báo cho bạn trước 30
              Ngày. Điều Khoản này được ban hành vào ngày 01 tháng 08 năm 2023.
            </span>
            <Row style={{ paddingTop: 16 }}>
              <strong
                style={{
                  fontSize: 16,
                }}
              >
                10. Other procedures/
              </strong>
              <strong
                style={{
                  fontSize: 16,
                  fontStyle: "italic",
                  color: "rgb(183, 183, 183)",
                }}
              >
                Các thủ tục khác
              </strong>
            </Row>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              1. Procedure to provide your consent and access your Personal Data
              <br></br> Upon downloading the App and register your account in
              the App, you agree to provide us with your Personal Data to
              collect and process your Personal Data.
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Quy trình cung cấp sự đồng ý của bạn và truy cập Dữ liệu cá nhân
              của bạn<br></br> Khi tải xuống Ứng Dụng và đăng ký tài khoản trên
              Ứng Dụng, bạn đồng ý cung cấp cho chúng tôi Dữ Liệu Cá Nhân của
              bạn để thu thập và xử lý Dữ Liệu Cá Nhân của bạn.
            </span>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              2. Procedure to withdraw your consent<br></br> Upon deleting your
              account in the App, this deletion of your account is considered as
              your consent withdrawal from processing your Personal Data,
              excluding our rights to have stored your Personal Data with
              respect to your Personal Data that was provided before you delete
              your account on the App
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Quy trình rút lại sự đồng ý của bạn<br></br> Khi xóa tài khoản của
              bạn trên Ứng Dụng, việc xóa tài khoản của bạn được coi là việc bạn
              rút lại sự đồng ý xử lý Dữ Liệu Cá Nhân của bạn, ngoại trừ quyền
              lưu trữ của chúng tôi đối với Dữ Liệu Cá Nhân của bạn đã được cung
              cấp trước khi bạn xóa tài khoản của bạn trên Ứng Dụng.
            </span>
            <span style={{ paddingLeft: 16, paddingTop: 16 }}>
              3. For further details<br></br> If you have any questions,
              requests and complaints relating to these Terms and the execution
              of these Terms, please contact us:
            </span>
            <span
              style={{
                paddingLeft: 16,
                paddingTop: 16,
                fontStyle: "italic",
                color: "rgb(183, 183, 183)",
              }}
            >
              Để biết thêm chi tiết<br></br> Nếu bạn có bất kỳ câu hỏi, yêu cầu
              và khiếu nại nào liên quan đến các Điều Khoản này và việc thực
              hiện các Điều Khoản này, vui lòng liên hệ với chúng tôi:
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
              }}
            >
              Hotline: (+84) 28 3740 2181
            </span>
            <span
              style={{
                paddingLeft: 32,
                paddingTop: 16,
              }}
            >
              Email: {""}
              <a href="mailto:cs-township.vn@gsenc.com">
                cs-township.vn@gsenc.com
              </a>
            </span>
          </Col>
        </Row>
      </Page>
    );
  }
}

PrivacyPolicy.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  login: makeSelectLogin(),
  buildingCluster: selectBuildingCluster(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "login", reducer });
const withSaga = injectSaga({ key: "login", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(PrivacyPolicy));
