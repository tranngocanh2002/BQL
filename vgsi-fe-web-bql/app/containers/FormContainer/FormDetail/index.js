/**
 *
 * FormDetail
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";
import messages from "./messages";

import Exception from "ant-design-pro/lib/Exception";
import { Button, Col, Form, Modal, Row, Table, Upload } from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import { config } from "../../../utils";
import {
  defaultAction,
  fetchDetailFormAction,
  updateDetailAction,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectFormDetail from "./selectors";

import styles from "./index.less";
const confirm = Modal.confirm;

import moment from "moment";

import TextArea from "antd/lib/input/TextArea";
import WithRole from "components/WithRole";
import { getFullLinkImage } from "connection";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { RELATIONSHIP_APARTMENT } from "utils/config";
const CollectionCreateForm = Form.create({ name: "form_in_modal" })(
  // eslint-disable-next-line
  class extends React.Component {
    render() {
      const { visible, onCancel, onDecline, form, intl } = this.props;
      const { getFieldDecorator } = form;
      const reasonPlaceholderText = intl.formatMessage({
        ...messages.reasonPlaceholder,
      });
      return (
        <Modal
          visible={visible}
          title={<FormattedMessage {...messages.cancelFormContent} />}
          okText={<FormattedMessage {...messages.agree} />}
          okType="danger"
          cancelText={<FormattedMessage {...messages.cancel} />}
          onCancel={onCancel}
          onOk={onDecline}
          destroyOnClose={true}
        >
          <Form layout="vertical">
            <Form.Item style={{ paddingBottom: 0, marginBottom: 0 }}>
              {getFieldDecorator("reason", {
                rules: [
                  {
                    required: true,
                    message: <FormattedMessage {...messages.reasonRequest} />,
                  },
                ],
              })(
                <TextArea
                  placeholder={reasonPlaceholderText}
                  maxLength={200}
                  rows={4}
                  style={{ margin: 0 }}
                />
              )}
            </Form.Item>
          </Form>
        </Modal>
      );
    }
  }
);
export class FormDetail extends React.PureComponent {
  constructor(props) {
    super(props);

    this.state = {
      record: (props.location.state || {}).record,
      visible: false,
      currentEdit: undefined,
      visibleUpdateStatus: false,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    let { params } = this.props.match;
    this.props.dispatch(fetchDetailFormAction({ id: params.id }));
  }

  componentWillReceiveProps(nextProps) {
    if (this.props.formDetail.detail.data != nextProps.formDetail.detail.data) {
      this.setState({
        record: nextProps.formDetail.detail.data,
        visible: false,
        visibleUpdateStatus: false,
      });
    }
  }
  handleDecline = () => {
    const { form } = this.formRef.props;
    form.validateFields((err, values) => {
      if (err) {
        return;
      }
      this.props.dispatch(
        updateDetailAction({
          id: this.state.record.id,
          status: 2,
          reason: values.reason,
          callback: () => {
            let { params } = this.props.match;

            this.props.dispatch(fetchDetailFormAction({ id: params.id }));
          },
        })
      );
      // this.setState(
      //   {
      //     currentEdit: {
      //       ...this.state.record,
      //     },
      //   },
      //   () => {
      //     this.setState({ visibleUpdateStatus: true });
      //   }
      // );
      form.resetFields();
      this.setState({ visibleUpdateStatus: false });
    });
  };

  saveFormRef = (formRef) => {
    this.formRef = formRef;
  };
  // _onDecline = () => {
  //   confirm({
  //     autoFocusButton: null,
  //     title: this.props.intl.formatMessage(messages.cancelFormContent),
  //     okText: this.props.intl.formatMessage(messages.agree),
  //     okType: "danger",
  //     cancelText: this.props.intl.formatMessage(messages.cancel),

  //     onOk: () => {
  //       this.props.dispatch(
  //         updateDetailAction({
  //           id: this.state.record.id,
  //           status: 2,
  //           callback: () => {
  //             let { params } = this.props.match;
  //             this.props.dispatch(fetchDetailFormAction({ id: params.id }));
  //           },
  //         })
  //       );
  //       this.setState(
  //         {
  //           currentEdit: {
  //             ...this.state.record,
  //           },
  //         },
  //         () => {
  //           this.setState({ visibleUpdateStatus: true });
  //         }
  //       );
  //     },

  //     onCancel() {},
  //   });
  // };
  handleCancel = () => {
    this.setState({ visibleUpdateStatus: false });
  };
  _onAccept = () => {
    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.approveRequest),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        this.props.dispatch(
          updateDetailAction({
            id: this.state.record.id,
            status: 1,
            callback: () => {
              let { params } = this.props.match;
              this.props.dispatch(fetchDetailFormAction({ id: params.id }));
            },
          })
        );
      },
      onCancel() {},
    });
  };
  render() {
    const { formDetail, dispatch, i18n, intl } = this.props;
    let { formatMessage } = this.props.intl;
    const { updating, detail } = formDetail;
    const elements = detail.data && detail.data.elements;
    const gender = [
      formatMessage(messages.male),
      formatMessage(messages.female),
    ];
    const paperType = [
      formatMessage(messages.passport),
      formatMessage(messages.cmnd),
      formatMessage(messages.cccd),
      formatMessage(messages.birthCertificate),
    ];
    const formType = [
      formatMessage(messages.registerCarCard),
      formatMessage(messages.registerResidentCard),
      formatMessage(messages.registerTransfer),
      formatMessage(messages.registerAccessCard),
    ];
    const formTitle = [
      formatMessage(messages.registrationInformation),
      formatMessage(messages.infoRegister),
      formatMessage(messages.transportInformation),
      formatMessage(messages.infoRegister),
    ];
    if (detail.data == -1) {
      return (
        <Page inner>
          <Exception
            type="404"
            desc={25656562}
            actions={
              <Button
                type="primary"
                onClick={() =>
                  this.props.history.push("/main/service-utility-form/list")
                }
              >
                {formatMessage(messages.back)}
              </Button>
            }
          />
        </Page>
      );
    }
    const worker = [
      {
        title: <span className={"nameTable"}>#</span>,

        width: "10%",
        key: "index",
        render: (record, text, index) => index + 1,
      },
      {
        align: "center",

        title: (
          <span className={"nameTable"}>{formatMessage(messages.worker)}</span>
        ),

        key: "goods",
        render: (record, text, index) => record[0],
      },
      {
        align: "center",
        width: "25%",
        title: (
          <span className={"nameTable"}>
            {formatMessage(messages.idNumber)}
          </span>
        ),

        key: "idNumber",
        render: (record, text, index) => <span>{record[1]}</span>,
      },
      {
        align: "center",
        width: "25%",
        title: (
          <span className={"nameTable"}>
            {formatMessage(messages.phoneNumber)}
          </span>
        ),

        key: "phoneNumber",
        render: (record, text, index) => <span>{record[2]}</span>,
      },
    ];
    const delivery = [
      {
        title: <span className={"nameTable"}>#</span>,

        width: "10%",
        key: "index",
        render: (record, text, index) => index + 1,
      },
      {
        align: "center",

        title: (
          <span className={"nameTable"}>
            {formatMessage(messages.itemType)}
          </span>
        ),

        key: "goods",
        render: (record, text, index) =>
          record.itemName ? record.itemName : record[0],
      },
      {
        align: "center",
        width: "25%",
        title: (
          <span className={"nameTable"}>
            {formatMessage(messages.characteristic)}
          </span>
        ),

        key: "abc",
        render: (record, text, index) => (
          <span>{record.itemSpec ? record.itemSpec : record[2]}</span>
        ),
      },
      {
        align: "center",
        width: "25%",
        title: (
          <span className={"nameTable"}>
            {formatMessage(messages.quantity)}
          </span>
        ),
        key: "quantiy",
        render: (record, text, index) => (
          <span>{record.itemNumber ? record.itemNumber : record[1]}</span>
        ),
      },
    ];
    return (
      <Page
        loading={detail.loading && !updating}
        inner
        className={styles.formDetailPage}
      >
        <div>
          <Row type="flex" justify="space-between" style={{ padding: 16 }}>
            <Col span={24} style={{ marginBottom: 48 }}>
              <Row type="flex" align="middle">
                <strong
                  style={{
                    textAlign: "left",
                    fontSize: 18,
                  }}
                >
                  {/* {formatMessage(messages.registrationInformation)}:{" "} */}
                  {formType[parseInt(detail.data.type || 0)]}
                </strong>
              </Row>
            </Col>
            <Col span={12}>
              <strong
                style={{
                  textAlign: "left",
                  fontSize: 18,
                }}
              >
                {formatMessage(messages.registrantInformation)}
              </strong>
            </Col>
            <Col span={12}>
              <strong
                style={{
                  fontSize: 18,
                }}
              >
                {formTitle[parseInt(detail.data.type || 0)]}
              </strong>
            </Col>
          </Row>

          <Row gutter={24} style={{ marginTop: 16, margin: 16 }}>
            <Col
              gutter={[12, 12]}
              span={12}
              style={{
                padding: "0px",
              }}
            >
              <Row style={{ marginTop: 12 }} span={12}>
                <Col span={6} style={{ fontWeight: "bold" }}>
                  {formatMessage(messages.createAt)}:
                </Col>
                <Col offset={1} span={6}>
                  {moment
                    .unix(detail.data.created_at)
                    .format("HH:mm, DD/MM/YYYY")}
                </Col>
              </Row>
              <Row style={{ marginTop: 12 }} span={12}>
                <Col span={6} style={{ fontWeight: "bold" }}>
                  {formatMessage(messages.customerName)}:
                </Col>
                <Col offset={1} span={6}>
                  {detail.data.resident_user_name}
                </Col>
              </Row>
              <Row style={{ marginTop: 12 }} span={12}>
                <Col span={6} style={{ fontWeight: "bold" }}>
                  {formatMessage(messages.propertyName)}:
                </Col>
                <Col offset={1} span={6}>
                  {detail.data.apartment_name}
                </Col>
              </Row>
              <Row style={{ marginTop: 12 }} span={12}>
                <Col span={6} style={{ fontWeight: "bold" }}>
                  {formatMessage(messages.phoneNumber)}:
                </Col>
                <Col offset={1} span={6}>
                  {detail.data.resident_user_phone &&
                    `0${detail.data.resident_user_phone.slice(-9)}`}
                </Col>
              </Row>
              <Row style={{ marginTop: 12 }} span={12}>
                <Col span={6} style={{ fontWeight: "bold" }}>
                  {formatMessage(messages.beneficiary)}:
                </Col>
                {elements &&
                  elements[elements.length - 1] &&
                  elements[elements.length - 1].options &&
                  elements[elements.length - 1].options.value && (
                    <Col offset={1} span={6}>
                      {elements[elements.length - 1].options.value}
                    </Col>
                  )}
              </Row>
              <Row style={{ marginTop: 48 }} span={12}>
                <Col span={6} style={{ fontWeight: "bold" }}>
                  {formatMessage(messages.status)}:
                </Col>
                <Col offset={1} span={6}>
                  {detail.data.status === 0
                    ? formatMessage(messages.waiting)
                    : detail.data.status === 1
                    ? formatMessage(messages.approve)
                    : detail.data.status === 2
                    ? formatMessage(messages.reject)
                    : formatMessage(messages.cancelled)}
                </Col>
              </Row>
              {detail.data.reason && (
                <Row style={{ marginTop: 12 }} span={12}>
                  <Col span={6} style={{ fontWeight: "bold" }}>
                    {formatMessage(messages.reason)}:
                  </Col>

                  <Col offset={1} span={6}>
                    {detail.data.reason}
                  </Col>
                </Row>
              )}
              <Row style={{ marginTop: 12 }} span={12}>
                <Col span={6} style={{ fontWeight: "bold" }}>
                  {formatMessage(messages.verifyUser)}:
                </Col>

                {(detail.data.status === 1 || detail.data.status === 2) && (
                  <Col offset={1} span={6}>
                    {detail.data.management_user_name}
                  </Col>
                )}
              </Row>
              <Row style={{ marginTop: 12 }} span={12}>
                <Col span={6} style={{ fontWeight: "bold" }}>
                  {formatMessage(messages.timeVerify)}:
                </Col>

                {(detail.data.status === 1 || detail.data.status === 2) && (
                  <Col offset={1} span={6}>
                    {moment
                      .unix(detail.data.agree_time)
                      .format("HH:mm, DD/MM/YYYY")}
                  </Col>
                )}
              </Row>
            </Col>
            <Col
              gutter={[12, 12]}
              span={12}
              style={{
                padding: "0px",
              }}
            >
              {detail.data.type === 2 && (
                <>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.transferType)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements &&
                      elements[0] &&
                      elements[0].options &&
                      elements[0].options.value === "1"
                        ? formatMessage(messages.moveOut)
                        : formatMessage(messages.moveIn)}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.transferPlace)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements &&
                      elements[1] &&
                      elements[1].options &&
                      elements[1].options.value === "1"
                        ? formatMessage(messages.lobby)
                        : formatMessage(messages.tunnel)}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.useTime)}:
                    </Col>
                    <Col offset={1} span={12}>
                      {elements && elements[3] ? elements[3].options.value : ""}
                      {" - "}
                      {elements && elements[4] ? elements[4].options.value : ""}
                    </Col>
                  </Row>
                  {/* <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.useElevator)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements &&
                      elements[2] &&
                      elements[2].options &&
                      elements[2].options.value === "true"
                        ? formatMessage(messages.yes)
                        : formatMessage(messages.no)}
                    </Col>
                  </Row> */}
                  <Row style={{ marginTop: 48 }} span={12}>
                    <Col span={12} style={{ fontWeight: "bold" }}>
                      {elements && elements[5]
                        ? formatMessage(messages.itemList)
                        : formatMessage(messages.itemList)}
                      :
                    </Col>
                  </Row>
                  <div style={{ height: 250, marginTop: 12 }}>
                    <Table
                      className="table1"
                      rowKey={(record, index) => index + 1}
                      columns={delivery}
                      style={{ color: "#0c0c97" }}
                      dataSource={elements && elements[5].option_table.body}
                      locale={{
                        emptyText: formatMessage(messages.itemType),
                      }}
                      pagination={false}
                      scroll={{ y: 200 }}
                      bordered
                    />
                  </div>
                </>
              )}
              {detail.data.type === 1 && (
                <>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.name)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[1] ? elements[1].options.value : ""}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.birthday)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[2]
                        ? moment
                            .unix(elements[2].options.value)
                            .format("DD/MM/YYYY")
                        : ""}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.gender)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {
                        gender[
                          parseInt(
                            (elements &&
                              elements[3] &&
                              elements[3].options &&
                              elements[3].options.value) ||
                              "0"
                          )
                        ]
                      }
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.phoneNumber)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[4] ? elements[4].options.value : ""}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.idCard)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[5] ? elements[5].options.value : ""}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.dateOfIssued)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[6]
                        ? moment
                            .unix(elements[6].options.value)
                            .format("DD/MM/YYYY")
                        : ""}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.issuedByIdCard)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[7] ? elements[7].options.value : ""}
                    </Col>
                  </Row>
                  {elements &&
                    elements[8] &&
                    elements[8].options &&
                    elements[8].options.value &&
                    !isNaN(elements[8].options.value) &&
                    parseInt(elements[8].options.value) >= 0 && (
                      <Row style={{ marginTop: 12 }} span={12}>
                        <Col span={6} style={{ fontWeight: "bold" }}>
                          {formatMessage(messages.relationship)}:
                        </Col>
                        <Col offset={1} span={6}>
                          {this.props.language == "vi"
                            ? RELATIONSHIP_APARTMENT[
                                parseInt(
                                  (elements &&
                                  elements[8] &&
                                  elements[8].options &&
                                  elements[8].options.value > 0
                                    ? elements[8].options.value
                                    : 0) || "0"
                                )
                              ].title
                            : RELATIONSHIP_APARTMENT[
                                parseInt(
                                  (elements &&
                                  elements[8] &&
                                  elements[8].options &&
                                  elements[8].options.value > 0
                                    ? elements[8].options.value
                                    : 0) || "0"
                                )
                              ].title_en}
                        </Col>
                      </Row>
                    )}
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.paperType)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {
                        paperType[
                          parseInt(
                            (elements &&
                              elements[9] &&
                              elements[9].options &&
                              elements[9].options.value) ||
                              "0"
                          )
                        ]
                      }
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.attachImage)}:
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 24, marginBottom: 12 }} span={12}>
                    <Col style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.residentImage)}:
                    </Col>

                    {/* <img
                      src={getFullLinkImage(
                        elements && elements[10]
                          ? elements[10].options.value
                          : ""
                      )}
                      width="40%"
                      height={"40%"}
                      style={{
                        paddingBottom: 15,
                      }}
                    /> */}
                  </Row>
                  <Row>
                    <Upload
                      className="ant-upload-list"
                      listType="picture-card"
                      fileList={
                        elements && elements[10]
                          ? elements[10].options.value
                              .split(",")
                              .map((image, index) => ({
                                uid: index,
                                url: getFullLinkImage(image, true),
                                status: "done",
                                name: image,
                              }))
                          : ""
                      }
                      render={<span>image</span>}
                      multiple
                      onRemove={false}
                      onPreview={this.handlePreview}
                      showUploadList={{
                        showRemoveIcon: false,
                        showDownloadIcon: false,
                      }}
                      onChange={this.handleChange}
                    />
                  </Row>
                  <Row style={{ marginTop: 12, marginBottom: 12 }} span={12}>
                    <Col style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.idImage)}:
                    </Col>
                  </Row>
                  {elements &&
                    elements[11] &&
                    elements[11].options &&
                    elements[11].options.value !== "" && (
                      <Upload
                        listType="picture-card"
                        fileList={
                          elements && elements[11]
                            ? elements[11].options.value
                                .split(",")
                                .map((image, index) => ({
                                  uid: index,
                                  url: getFullLinkImage(image, true),
                                  status: "done",
                                  name: image,
                                }))
                            : ""
                        }
                        showUploadList={{
                          showRemoveIcon: false,
                          showDownloadIcon: false,
                        }}
                        onPreview={this.handlePreview}
                        onChange={this.handleChange}
                      />
                    )}
                  <Row style={{ marginTop: 12, marginBottom: 12 }} span={12}>
                    <Col style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.livingImage)}:
                    </Col>
                  </Row>
                  {elements &&
                    elements[12] &&
                    elements[12].options &&
                    elements[12].options.value !== "" && (
                      <Upload
                        listType="picture-card"
                        fileList={
                          elements && elements[12]
                            ? elements[12].options.value
                                .split(",")
                                .map((image, index) => ({
                                  uid: index,
                                  url: getFullLinkImage(image, true),
                                  status: "done",
                                  name: image,
                                }))
                            : ""
                        }
                        showUploadList={{
                          showRemoveIcon: false,
                          showDownloadIcon: false,
                        }}
                        onRemove={false}
                        onPreview={this.handlePreview}
                        onChange={this.handleChange}
                      />
                    )}
                </>
              )}
              {detail.data.type === 3 && (
                <>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.constructionName)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[1] ? elements[1].options.value : ""}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.phoneNumber)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[2] ? elements[2].options.value : ""}

                      {/* {elements && elements[2]
                        ? moment
                            .unix(elements[2].options.value)
                            .format("DD/MM/YYYY")
                        : ""} */}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.constructionContact)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[3] ? elements[3].options.value : ""}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.contact)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[4] ? elements[4].options.value : ""}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.timeConstruct)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[5]
                        ? moment
                            .unix(elements[5].options.value)
                            .format("DD/MM/YYYY")
                        : ""}
                      {" - "}
                      {elements && elements[6]
                        ? moment
                            .unix(elements[6].options.value)
                            .format("DD/MM/YYYY")
                        : ""}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.workItem)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[7] ? elements[7].options.value : ""}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.workersList)}:
                    </Col>
                  </Row>
                  <div style={{ height: 250, marginTop: 12 }}>
                    <Table
                      className="table1"
                      rowKey={(record, index) => index + 1}
                      columns={worker}
                      style={{ color: "#0c0c97" }}
                      dataSource={elements && elements[8].option_table.body}
                      locale={{
                        emptyText: formatMessage(messages.itemType),
                      }}
                      pagination={false}
                      scroll={{ y: 200 }}
                    />
                  </div>
                </>
              )}
              {detail.data.type === 0 && (
                <>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.phuongTien)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[0]
                        ? elements[0].options.value === "0"
                          ? formatMessage(messages.oTo)
                          : elements[0].options.value === "1"
                          ? formatMessage(messages.moto)
                          : formatMessage(messages.xeDap)
                        : ""}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.bienSo)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[1] ? elements[1].options.value : ""}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.nhanHieu)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[2] ? elements[2].options.value : ""}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.mauXe)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[3] ? elements[3].options.value : ""}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.name)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[4] ? elements[4].options.value : ""}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.birthday)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[5]
                        ? moment
                            .unix(elements[5].options.value)
                            .format("DD/MM/YYYY")
                        : ""}
                    </Col>
                  </Row>

                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.phoneNumber)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[6] ? elements[6].options.value : ""}
                    </Col>
                  </Row>
                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.idCard)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {elements && elements[7] ? elements[7].options.value : ""}
                    </Col>
                  </Row>

                  <Row style={{ marginTop: 12 }} span={12}>
                    <Col span={6} style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.relationship)}:
                    </Col>
                    <Col offset={1} span={6}>
                      {this.props.language == "vi"
                        ? RELATIONSHIP_APARTMENT[
                            parseInt(
                              (elements &&
                              elements[8] &&
                              elements[8].options &&
                              elements[8].options.value > 0
                                ? elements[8].options.value
                                : 0) || "0"
                            )
                          ].title
                        : RELATIONSHIP_APARTMENT[
                            parseInt(
                              (elements &&
                              elements[8] &&
                              elements[8].options &&
                              elements[8].options.value > 0
                                ? elements[8].options.value
                                : 0) || "0"
                            )
                          ].title_en}
                    </Col>
                  </Row>
                </>
              )}
            </Col>
          </Row>
          {
            <WithRole
              roles={[
                config.ALL_ROLE_NAME
                  .MANAGE_FORM_REGISTRATION_SERVICE_UTILITY_FORM_CHANGESTATUS,
              ]}
            >
              {detail.data.status == 0 && (
                <Col offset={8} style={{ padding: 0 }}>
                  <Button
                    type="danger"
                    onClick={() => this.setState({ visibleUpdateStatus: true })}
                  >
                    {formatMessage(messages.reject)}
                  </Button>
                  <span style={{ paddingLeft: 24 }}>
                    <Button type="primary" onClick={this._onAccept}>
                      {formatMessage(messages.approve)}
                    </Button>
                  </span>
                </Col>
              )}
            </WithRole>
          }
          <CollectionCreateForm
            intl={intl}
            wrappedComponentRef={this.saveFormRef}
            visible={this.state.visibleUpdateStatus}
            onCancel={this.handleCancel}
            onDecline={this.handleDecline}
          />
        </div>
      </Page>
    );
  }
}

FormDetail.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  formDetail: makeSelectFormDetail(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "formDetail", reducer });
const withSaga = injectSaga({ key: "formDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(FormDetail));
