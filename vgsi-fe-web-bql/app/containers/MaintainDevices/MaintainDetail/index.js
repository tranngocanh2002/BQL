/**
 *
 * MaintainDetail
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";
import Upload from "../../../components/Uploader";

import { Col, Form, Modal, Row } from "antd";
import { withRouter } from "react-router";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import messages from "../messages";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectMaintainDetail from "./selectors";

const confirm = Modal.confirm;

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import moment from "moment";
import QRCode from "react-qr-code";
import config from "../../../utils/config";
import { defaultAction, fetchEquipmentDetailAction } from "./actions";
const formItemLayout = {
  labelCol: {
    xs: { span: 24 },
    sm: { span: 4 },
    md: { span: 6 },
    lg: { span: 6 },
    xl: { span: 4 },
  },
  wrapperCol: {
    span: 14,
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class MaintainDetail extends React.PureComponent {
  constructor(props) {
    super(props);
    let record = (props.location.state || {}).record;
    this.state = {
      record,
      imageUrl: record ? record.avatar : undefined,

      uploadFileError: false,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    const { id } = this.props.match.params;
    this.props.dispatch(fetchEquipmentDetailAction({ id: id }));
  }

  componentWillReceiveProps(nextProps) {
    if (this.props.MaintainDetail.record != nextProps.MaintainDetail.record) {
      this.setState({
        record: nextProps.MaintainDetail.record,
        imageUrl: nextProps.MaintainDetail.record.avatar,
      });
    }
  }

  handerCancel = () => {
    this.props.history.push("/main/maintain/list", { pos: 1 });
  };

  handlerUpdate = () => {
    const { dispatch, form, userDetail } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
    });
  };
  handleOk = () => {
    const { dispatch, form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
    });
  };
  render() {
    const URL_API = "http://localhost";
    const { MaintainDetail } = this.props;
    const { formatMessage } = this.props.intl;
    const { getFieldDecorator } = this.props.form;
    const { detail } = MaintainDetail;
    const { record } = this.state;
    return (
      <Page inner loading={detail.loading}>
        <div>
          <Row type="flex" justify="space-between" style={{ padding: 16 }}>
            <Col span={24}>
              {/* <Row>
                <span
                  onClick={(e) => {
                    e.preventDefault();
                    this.props.history.push("/main/maintain/list", { pos: 1 });
                  }}
                  style={{
                    display: "flex",
                    alignItems: "center",
                    cursor: "pointer",
                    color: GLOBAL_COLOR,
                    paddingBottom: 16,
                  }}
                >
                  <i className="material-icons">keyboard_backspace</i>
                  &ensp; {formatMessage(messages.back)}
                </span>
              </Row> */}
              <Row type="flex" align="middle">
                <strong
                  style={{
                    textAlign: "left",
                    fontSize: 24,
                  }}
                >
                  {record.name || ""}
                </strong>
              </Row>
            </Col>
          </Row>
          <Row gutter={24} style={{ margin: 16 }}>
            <Col
              span={6}
              style={{
                padding: "0px",
              }}
            >
              <Row className="rowItem" style={{ marginTop: 12 }}>
                <Col span={12}>
                  <strong>{formatMessage(messages.deviceCode)}:</strong>
                </Col>
              </Row>
              <Row className="rowItem" style={{ marginTop: 12 }}>
                <Col span={12}>
                  <strong>{formatMessage(messages.deviceType)}:</strong>
                </Col>
              </Row>
              <Row className="rowItem" style={{ marginTop: 12 }}>
                <Col span={12}>
                  <strong>{formatMessage(messages.createAt)}:</strong>
                </Col>
              </Row>
              <Row className="rowItem" style={{ marginTop: 12 }}>
                <Col span={12}>
                  <strong>{formatMessage(messages.status)}:</strong>
                </Col>
              </Row>
            </Col>
            <Col
              span={18}
              style={{
                padding: "0px",
              }}
            >
              <Row className="rowItem" style={{ marginTop: 12 }}>
                <Col span={8}> {detail.data && detail.data.code}</Col>
                <Col span={16}>
                  <Row className="rowItem">
                    <Col span={10}>
                      <strong>{formatMessage(messages.warrantyPeriod)}:</strong>
                    </Col>
                    {detail.data && detail.data.guarantee_time_start && (
                      <Col offset={1} span={12}>
                        {moment
                          .unix(detail.data && detail.data.guarantee_time_start)
                          .format("DD/MM/YYYY") || ""}
                        {" - "}
                        {moment
                          .unix(detail.data && detail.data.guarantee_time_end)
                          .format("DD/MM/YYYY") || ""}
                      </Col>
                    )}
                  </Row>
                </Col>
              </Row>
              <Row className="rowItem" style={{ marginTop: 12 }}>
                <Col span={8}>
                  {detail.data &&
                    (this.props.language === "en"
                      ? config.MAINTAIN_DEVICES[detail.data.type].name_en
                      : config.MAINTAIN_DEVICES[detail.data.type].name)}
                </Col>
                <Col span={16}>
                  <Row className="rowItem">
                    <Col span={10}>
                      <strong>
                        {formatMessage(messages.lastMaintenanceTime)}:
                      </strong>
                    </Col>
                    <Col offset={1} span={12}>
                      {detail.data && detail.data.maintenance_time_last
                        ? moment
                            .unix(
                              detail.data && detail.data.maintenance_time_last
                            )
                            .format("DD/MM/YYYY")
                        : ""}
                    </Col>
                  </Row>
                </Col>
              </Row>
              <Row className="rowItem" style={{ marginTop: 12 }}>
                <Col span={8}>
                  {moment
                    .unix(detail.data && detail.data.maintenance_time_start)
                    .format("DD/MM/YYYY") || ""}
                </Col>
                <Col span={16}>
                  <Row className="rowItem">
                    <Col span={10}>
                      <strong>
                        {formatMessage(messages.timeMaintenance)}:
                      </strong>
                    </Col>
                    <Col offset={1} span={12}>
                      {moment
                        .unix(detail.data && detail.data.maintenance_time_start)
                        .format("DD/MM/YYYY") || ""}
                    </Col>
                  </Row>
                </Col>
              </Row>
              <Row className="rowItem" style={{ marginTop: 12 }}>
                <Col span={8}>
                  {detail.data &&
                    (detail.data.status === 0
                      ? formatMessage(messages.inActive)
                      : formatMessage(messages.active))}
                </Col>
                <Col span={16}>
                  <Row className="rowItem">
                    <Col span={10}>
                      <strong>
                        {formatMessage(messages.repeatedMaintenance)}:
                      </strong>
                    </Col>
                    <Col offset={1} span={12}>
                      {detail.data &&
                        (this.props.language === "en"
                          ? config.MAINTAIN_DEVICES_TERM[detail.data.cycle]
                              .name_en
                          : config.MAINTAIN_DEVICES_TERM[detail.data.cycle]
                              .name)}
                    </Col>
                  </Row>
                </Col>
              </Row>
            </Col>
          </Row>
          <Col style={{ marginTop: 16, margin: 16 }}>
            <Row className="rowItem" style={{ marginTop: 12 }}>
              <Col span={6}>
                <strong>{formatMessage(messages.location)}:</strong>
              </Col>
              <Col span={18}> {detail.data && detail.data.position}</Col>
            </Row>
            <Row className="rowItem" style={{ marginTop: 12 }}>
              <Col span={6}>
                <strong>{formatMessage(messages.description)}:</strong>
              </Col>
              <Col span={18}>{detail.data && detail.data.description}</Col>
            </Row>
            <Row className="rowItem" style={{ marginTop: 12 }}>
              <Col span={6}>
                <strong>{formatMessage(messages.qrCode)}:</strong>
              </Col>
              <Col span={18}>
                <QRCode
                  size={128}
                  value={`${URL_API}/main/maintain/detail/${record.id}`}
                  viewBox={"0 0 128 128"}
                />
              </Col>
            </Row>
            <Row style={{ marginTop: 12 }}>
              <Col span={6} style={{ fontWeight: "bold" }}>
                {formatMessage(messages.fileAttach)}:
              </Col>
              <Col span={18}>
                <Upload
                  className="ant-upload-list"
                  listType="picture-card"
                  fileList={
                    detail.data &&
                    detail.data.attach &&
                    detail.data.attach.fileList
                  }
                  onRemove={false}
                  showUploadList={{
                    showDownloadIcon: false,
                    showRemoveIcon: false,
                  }}
                />
              </Col>
            </Row>
          </Col>
          {/* {record.status == 0 && (
            <Col offset={8} style={{ padding: 0 }}>
              <Button type="danger" onClick={this._onDecline}>
                {formatMessage(messages.reject)}
              </Button>
              <span style={{ paddingLeft: 24 }}>
                <Button type="primary" onClick={this._onAccept}>
                  {formatMessage(messages.approve)}
                </Button>
              </span>

            </Col>
          )} */}
        </div>
      </Page>
    );
  }
}

MaintainDetail.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  MaintainDetail: makeSelectMaintainDetail(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "maintainDetail", reducer });
const withSaga = injectSaga({ key: "maintainDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(MaintainDetail)));
