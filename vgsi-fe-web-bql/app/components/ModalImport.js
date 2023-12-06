/*
 * Created by duydatpham@gmail.com on 17/09/2019
 * Copyright (c) 2019 duydatpham@gmail.com
 */
import React from "react";
import { Modal, Progress, Row, Col, Table, Button, Icon } from "antd";
import Uploader from "./Uploader";
import { injectIntl } from "react-intl";
import messages from "./messages";
// import { _  } from "../../server/argv";
import _ from "lodash";
import { createStructuredSelector } from "reselect";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { connect } from "react-redux";
import { translateErrorMessage } from "utils";

export class ModalImport extends React.PureComponent {
  state = {
    visible: false,
    percent: -1,
    error: false,
    promise: false,
    onComplete: undefined,
  };
  componentDidMount() {
    window.modalImport = this;
  }
  componentWillUnmount() {
    window.modalImport = undefined;
  }

  show = (promise, onComplete) => {
    this.setState({
      visible: true,
      error: false,
      percent: -1,
      promise,
      onComplete,
      dataSuccess: undefined,
    });
    // setTimeout(() => {
    //   $("span.ant-upload").find("input").click();
    // }, 100);
  };

  render() {
    const columns = [
      {
        title: <span>{this.props.intl.formatMessage(messages.line)}</span>,
        dataIndex: "row",
        key: "row",
      },
      {
        title: (
          <span>{this.props.intl.formatMessage(messages.contentError)}</span>
        ),
        dataIndex: "message",
        key: "message",
        render: (text, record) => (
          <>
            {record.message.map((value, index) => (
              <>
                <span key={index}>{value}</span>
                <br></br>
              </>
            ))}
          </>
        ),
      },
    ];

    const { visible, percent, error, promise, onComplete, dataSuccess } =
      this.state;
    let data = [];
    if (dataSuccess) {
      Object.keys(dataSuccess).forEach((key) => {
        if (Array.isArray(dataSuccess[key])) {
          dataSuccess[key].forEach((dd) => {
            let newDD = { ...dd };
            delete newDD.line;
            data.push({
              key: data.length,
              row: dd.line,
              message:
                _.flatten([
                  translateErrorMessage(newDD.message, this.props.language),
                ]) || [],
            });
          });
        }
      });
    }
    return (
      <Modal
        visible={visible}
        // centered
        closable={percent == -1 || percent == 100}
        footer={
          percent == 100 || !!error ? (
            <Row>
              <Button
                loading={!(percent == 100 || !!error)}
                type="primary"
                onClick={() => {
                  this.setState({ visible: false });
                }}
              >
                {this.props.intl.formatMessage(messages.close)}
              </Button>
            </Row>
          ) : null
        }
        onCancel={() => {
          (percent == -1 || percent == 100) &&
            this.setState({ visible: false });
        }}
        title={this.props.intl.formatMessage(messages.importData)}
        width={"50%"}
      >
        <Col style={{ textAlign: "center" }}>
          <Row style={{ marginTop: 24, marginBottom: 24 }}>
            {percent != -1 && (
              <Progress
                type="circle"
                percent={error ? 100 : percent}
                status={
                  error ? "exception" : percent == 100 ? "success" : "normal"
                }
              />
            )}
            <Uploader
              acceptList={[
                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                ".xls",
                ".xlsx",
              ]}
              accept={".xls,.xlsx,.csv"}
              onUploaded={(url) => {
                console.log("url ", url);
                !!promise &&
                  promise(url).then((res) => {
                    if (res.success) {
                      const _interval = setInterval(() => {
                        let percent = Math.min(
                          100,
                          this.state.percent + parseInt(Math.random() * 8)
                        );
                        this.setState({
                          percent,
                        });

                        if (percent == 100) {
                          clearInterval(_interval);
                          this.setState({
                            dataSuccess: res.data,
                          });
                          !!onComplete && onComplete();
                        }
                      }, 1000);
                    } else {
                      this.setState({
                        error: translateErrorMessage(
                          res.message,
                          this.props.language
                        ),
                        percent: 100,
                      });
                    }
                  });
              }}
              onProgress={(e) => {
                console.log("percent ", e);
                this.setState({
                  percent: error ? 100 : parseInt((e.percent * 80) / 100),
                });
              }}
              onErrorCallback={(message) => {
                this.setState({
                  error: translateErrorMessage(message, this.props.language),
                  percent: 100,
                });
              }}
            >
              {percent == -1 && (
                <div
                  style={{
                    width: 128,
                    height: 128,
                    border: "1px dashed",
                    borderRadius: 8,
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    cursor: "pointer",
                  }}
                >
                  <Icon type={"plus"} style={{ fontSize: 32 }} />
                </div>
              )}
            </Uploader>
          </Row>
          {
            <Row
              style={{
                marginTop: 16,
                paddingBottom: 24,
              }}
            >
              <span style={{ color: "black", fontSize: 18 }}>
                {error
                  ? error
                  : percent == -1
                  ? this.props.intl.formatMessage(messages.chooseFileImport)
                  : percent < 80
                  ? this.props.intl.formatMessage(messages.uploadingFile)
                  : percent < 100
                  ? this.props.intl.formatMessage(messages.importingFile)
                  : ""}
              </span>
            </Row>
          }

          {percent == 100 && !error && (
            <Row>
              <Col span={8} style={{ paddingTop: 24, paddingBottom: 24 }}>
                <Row style={{ textAlign: "center" }}>
                  <span style={{ fontSize: 16, color: "black" }}>
                    {this.props.intl.formatMessage(messages.total)}
                  </span>
                  <br />
                  <span
                    style={{ fontSize: 32, fontWeight: "bold", color: "black" }}
                  >
                    {dataSuccess ? dataSuccess.TotalRow || 0 : 0}
                  </span>
                </Row>
              </Col>
              <Col
                span={8}
                style={{
                  borderLeft: "1px solid rgba(210, 210, 210, 0.5)",
                  borderRight: "1px solid rgba(210, 210, 210, 0.5)",
                  paddingTop: 24,
                  paddingBottom: 24,
                }}
              >
                <Row style={{ textAlign: "center" }}>
                  <span style={{ fontSize: 16, color: "black" }}>
                    {this.props.intl.formatMessage(messages.success)}
                  </span>
                  <br />
                  <span
                    style={{
                      fontSize: 32,
                      fontWeight: "bold",
                      color: "#3EA671",
                    }}
                  >
                    {dataSuccess ? dataSuccess.TotalImport || 0 : 0}
                  </span>
                </Row>
              </Col>
              <Col span={8} style={{ paddingTop: 24, paddingBottom: 24 }}>
                <Row style={{ textAlign: "center" }}>
                  <span style={{ fontSize: 16, color: "black" }}>
                    {this.props.intl.formatMessage(messages.error)}
                  </span>
                  <br />
                  <span
                    style={{
                      fontSize: 32,
                      fontWeight: "bold",
                      color: "#D85357",
                    }}
                  >
                    {dataSuccess
                      ? (dataSuccess.TotalRow || 0) -
                        (dataSuccess.TotalImport || 0)
                      : 0}
                  </span>
                </Row>
              </Col>
            </Row>
          )}
          {percent == 100 && !error && (
            <Table columns={columns} dataSource={data} bordered />
          )}
        </Col>
      </Modal>
    );
  }
}

const mapStateToProps = createStructuredSelector({
  language: makeSelectLocale(),
});

const withConnect = connect(mapStateToProps);

export default injectIntl(withConnect(ModalImport));
