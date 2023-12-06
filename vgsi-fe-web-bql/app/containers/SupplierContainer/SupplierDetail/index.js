/**
 *
 * SupplierDetail
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";
import Exception from "ant-design-pro/lib/Exception";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../components/Page/Page";
import {
  defaultAction,
  fetchAllSupplierAction,
  fetchSupplierDetailAction,
} from "./actions";
import { Row, Modal, Col, Upload, Button } from "antd";
import styles from "./index.less";

const confirm = Modal.confirm;
import queryString from "query-string";
import makeSelectSupplierDetail from "./selectors";
import { config } from "../../../utils";

import { getFullLinkImage } from "../../../connection";
import moment from "moment";
import WithRole from "../../../components/WithRole";
import { GLOBAL_COLOR } from "../../../utils/constants";
import { injectIntl } from "react-intl";
import messages from "../messages";

/* eslint-disable react/prefer-stateless-function */
export class SupplierDetail extends React.PureComponent {
  constructor(props) {
    super(props);

    this.state = {
      record: (props.location.state || {}).record,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
    this._unmounted = true;
  }

  componentDidMount() {
    let { params } = this.props.match;
    this.props.dispatch(fetchSupplierDetailAction({ id: params.id }));
  }

  componentWillReceiveProps(nextProps) {
    if (this.props.supplierDetail.detail != nextProps.supplierDetail.detail) {
      this.setState({
        record: nextProps.supplierDetail.detail,
      });
    }
  }

  _onView = (record) => {
    this.props.history.push(`/main/contractor/detail?${record.id}`, { record });
  };

  render() {
    const { supplierDetail } = this.props;

    const { loading, detail } = supplierDetail;

    const recordSupplier = this.state.record;
    console.log(recordSupplier.description.split("\n"));

    if (detail === -1) {
      return (
        <Page loading={loading} inner={!loading}>
          <Exception
            type="404"
            desc={this.props.intl.formatMessage(messages.noData)}
            actions={
              <Button
                type="primary"
                onClick={() => this.props.history.push("/main/contractor/list")}
              >
                {this.props.intl.formatMessage(messages.btnBack)}
              </Button>
            }
          />
        </Page>
      );
    }

    return (
      <Page inner className={styles.contractorListPage}>
        <div>
          <Row style={{ padding: 16 }}>
            <Col style={{ marginBottom: 16 }}>
              <strong style={{ fontSize: 18 }}>
                {this.props.intl.formatMessage(messages.informationSupplier)}
              </strong>
            </Col>
            <Col span={24}>
              <Row type="flex" justify="space-between">
                <Col span={16}>
                  <Col span={6}>
                    <strong>
                      {this.props.intl.formatMessage(messages.supplierName)}:
                    </strong>
                  </Col>
                  <Col span={18}>{recordSupplier && recordSupplier.name}</Col>
                </Col>
              </Row>
              <Row
                type="flex"
                justify="space-between"
                style={{ marginTop: 24 }}
              >
                <Col span={16}>
                  <Col span={6}>
                    <strong>
                      {this.props.intl.formatMessage(messages.status)}:
                    </strong>
                  </Col>
                  <Col span={18}>
                    {recordSupplier && recordSupplier.status === 0
                      ? this.props.intl.formatMessage(messages.inactive)
                      : this.props.intl.formatMessage(messages.active)}
                  </Col>
                </Col>
              </Row>
              <Row
                type="flex"
                justify="space-between"
                style={{ marginTop: 24 }}
              >
                <Col span={16}>
                  <Col span={6}>
                    <strong>
                      {this.props.intl.formatMessage(messages.address)}:
                    </strong>
                  </Col>
                  <Col span={18}>
                    {recordSupplier && recordSupplier.address}
                  </Col>
                </Col>
              </Row>
              <Row
                type="flex"
                justify="space-between"
                style={{ marginTop: 24 }}
              >
                <Col span={16}>
                  <Col span={6}>
                    <strong>
                      {this.props.intl.formatMessage(messages.description)}:
                    </strong>
                  </Col>
                  <Col span={18}>
                    <span style={{ whiteSpace: "pre-wrap" }}>
                      {recordSupplier && recordSupplier.description}
                    </span>
                  </Col>
                </Col>
              </Row>
              <Row
                type="flex"
                justify="space-between"
                style={{ marginTop: 24 }}
              >
                <Col span={16}>
                  <Col span={6} style={{ marginTop: 8 }}>
                    <strong>
                      {this.props.intl.formatMessage(messages.attachFile)}:
                    </strong>
                  </Col>
                  <Col span={18}>
                    <Upload
                      fileList={
                        recordSupplier &&
                        recordSupplier.attach &&
                        recordSupplier.attach.fileList &&
                        recordSupplier.attach.fileList.map(function (image) {
                          return {
                            uid: image.uid,
                            name: image.name,
                            status: "done",
                            url: getFullLinkImage(image.url),
                          };
                        })
                      }
                      onRemove={false}
                      showUploadList={{
                        showDownloadIcon: false,
                        showRemoveIcon: false,
                      }}
                    />
                  </Col>
                </Col>
              </Row>
              <Col style={{ marginTop: 32 }}>
                <strong style={{ fontSize: 18 }}>
                  {this.props.intl.formatMessage(messages.informationContact)}
                </strong>
              </Col>
              <Row
                type="flex"
                justify="space-between"
                style={{ marginTop: 24 }}
              >
                <Col span={16}>
                  <Col span={6}>
                    <strong>
                      {this.props.intl.formatMessage(messages.fullName)}:
                    </strong>
                  </Col>
                  <Col span={18}>
                    {recordSupplier && recordSupplier.contact_name}
                  </Col>
                </Col>
              </Row>
              <Row
                type="flex"
                justify="space-between"
                style={{ marginTop: 24 }}
              >
                <Col span={16}>
                  <Col span={6}>
                    <strong>
                      {this.props.intl.formatMessage(messages.phone)}:
                    </strong>
                  </Col>
                  <Col span={18}>
                    {recordSupplier && recordSupplier.contact_phone
                      ? `0${recordSupplier.contact_phone.slice(-9)}`
                      : ""}
                  </Col>
                </Col>
              </Row>
              <Row
                type="flex"
                justify="space-between"
                style={{ marginTop: 24 }}
              >
                <Col span={16}>
                  <Col span={6}>
                    <strong>Email:</strong>
                  </Col>
                  <Col span={18}>
                    {recordSupplier && recordSupplier.contact_email}
                  </Col>
                </Col>
              </Row>
            </Col>
          </Row>
        </div>
      </Page>
    );
  }
}

SupplierDetail.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  supplierDetail: makeSelectSupplierDetail(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "supplierDetail", reducer });
const withSaga = injectSaga({ key: "supplierDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(SupplierDetail));
