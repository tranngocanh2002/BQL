/**
 *
 * CombineCardDetail
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import Exception from "ant-design-pro/lib/Exception";
import { Button, Col, Modal, Row, Typography, Avatar } from "antd";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import moment from "moment";
import { FormattedMessage, injectIntl } from "react-intl";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import WithRole from "../../../components/WithRole";
import { selectAuthGroup } from "../../../redux/selectors";
import { getFullLinkImage } from "connection";

import {
  defaultAction,
  fetchApartmentAction,
  fetchDetailCombineCardAction,
} from "./actions";
import messages, { scope } from "../messages";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectCombineCardDetail from "./selectors";
import styles from "./index.less";
const { Title, Paragraph } = Typography;

/* eslint-disable react/prefer-stateless-function */
export class CombineCardDetail extends React.PureComponent {
  constructor(props) {
    super(props);

    this.state = {
      record: (props.location.state || {}).record,
      visible: false,
      visibleUpdateMember: false,
      currentEdit: undefined,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    let { params } = this.props.match;
    this.props.dispatch(fetchDetailCombineCardAction({ id: params.id }));
    this.props.dispatch(fetchApartmentAction());
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.combineCardDetail.detail != nextProps.combineCardDetail.detail
    ) {
      this.setState({
        record: nextProps.combineCardDetail.detail,
        // visible: false,
        visibleUpdateMember: false,
      });
    }
    if (
      this.props.combineCardDetail.success !=
        nextProps.combineCardDetail.success &&
      nextProps.combineCardDetail.success
    ) {
      this.setState({
        visible: false,
      });
    }
  }

  _onEditType = (record) => {
    this.setState(
      {
        currentEdit: {
          ...record,
        },
      },
      () => {
        this.setState({ visibleUpdateMember: true });
      }
    );
  };

  render() {
    let { formatMessage } = this.props.intl;
    const { combineCardDetail, language, userDetail, auth_group } = this.props;
    const { detail, activities } = combineCardDetail;

    if (detail.data == -1) {
      return (
        <Page inner>
          <Exception
            type="404"
            desc={formatMessage(messages.notFindPage)}
            actions={
              <Button
                type="primary"
                onClick={() => this.props.history.push("/main/merge-card/list")}
              >
                {formatMessage(messages.back)}
              </Button>
            }
          />
        </Page>
      );
    }
    return (
      <Page loading={detail.loading} inner={detail.loading}>
        <div className={styles.combineCardDetailPage}>
          <Row>
            <Col
              span={16}
              style={{ marginRight: 12 }}
              className={styles.combineCardDetail}
            >
              <Title level={3}> {formatMessage(messages.cardInfo)}</Title>

              <Row type="flex" justify="start" align="middle">
                <Col span={4}>
                  <Typography className={styles.infoTitle}>
                    {formatMessage(messages.cardId).toUpperCase()}:
                  </Typography>
                </Col>

                <Typography className={styles.info}>
                  {detail.data && detail.data.code}
                </Typography>
              </Row>
              <Row
                type="flex"
                justify="start"
                align="middle"
                style={{ marginTop: 16 }}
              >
                <Col span={4}>
                  <Typography className={styles.infoTitle}>
                    {formatMessage(messages.cardNumber).toUpperCase()}:
                  </Typography>
                </Col>

                <Typography className={styles.info}>
                  {detail.data && detail.data.number}
                </Typography>
              </Row>
              {detail.data && detail.data.status != 0 && (
                <Row
                  type="flex"
                  justify="start"
                  align="middle"
                  style={{ marginTop: 16 }}
                >
                  <Col span={4}>
                    <Typography className={styles.infoTitle}>
                      {formatMessage(messages.apartmentName).toUpperCase()}:
                    </Typography>
                  </Col>
                  <Typography className={styles.info}>
                    {detail.data && detail.data.apartment_name}
                  </Typography>
                </Row>
              )}
              {detail.data && detail.data.status != 0 && (
                <Row
                  type="flex"
                  justify="start"
                  align="middle"
                  style={{ marginTop: 16 }}
                >
                  <Col span={4}>
                    <Typography className={styles.infoTitle}>
                      {formatMessage(messages.cardOwner).toUpperCase()}:
                    </Typography>
                  </Col>
                  <Typography className={styles.info}>
                    {detail.data.resident_user_name}
                  </Typography>
                </Row>
              )}
              <Row
                type="flex"
                justify="start"
                align="middle"
                style={{ marginTop: 16 }}
              >
                <Col span={4}>
                  <Typography className={styles.infoTitle}>
                    {formatMessage(messages.createAt).toUpperCase()}:
                  </Typography>
                </Col>
                <Typography className={styles.info}>
                  {detail.data &&
                    detail.data.created_at &&
                    moment
                      .unix(detail.data.created_at)
                      .format("HH:mm, DD/MM/YYYY")}
                </Typography>
              </Row>
              <Row
                type="flex"
                justify="start"
                align="middle"
                style={{ marginTop: 16 }}
              >
                <Col span={4}>
                  <Typography className={styles.infoTitle}>
                    {formatMessage(messages.updateTime).toUpperCase()}:
                  </Typography>
                </Col>
                <Typography className={styles.info}>
                  {detail.data &&
                    detail.data.updated_at &&
                    moment
                      .unix(detail.data.updated_at)
                      .format("HH:mm, DD/MM/YYYY")}
                </Typography>
              </Row>
            </Col>
            <Col span={7} className={styles.combineCardDetail}>
              <Title level={3}>
                {formatMessage(messages.history).toUpperCase()}:
              </Title>
              <Row
                type="flex"
                align="middle"
                style={{
                  marginTop: 4,
                }}
              >
                <Avatar
                  src={getFullLinkImage(
                    "https://images.unsplash.com/photo-1664308703521-724bcdcdf39b?q=80&w=1972&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                  )}
                  icon="user"
                />

                <Col span={16} style={{ marginLeft: 12 }}>
                  <Row type="flex" justify="space-between" align="middle">
                    <Col>
                      <Typography className={styles.infoTitle}>BQL</Typography>
                    </Col>
                    <Typography className={styles.info}>
                      {detail.data &&
                        detail.data.created_at &&
                        moment
                          .unix(detail.data.created_at)
                          .format("HH:mm, DD/MM/YYYY")}
                    </Typography>
                  </Row>
                  <Typography className={styles.info}>
                    {"Đã kích hoạt thẻ"}
                  </Typography>
                </Col>
              </Row>
              <Row
                type="flex"
                align="middle"
                style={{
                  marginTop: 4,
                }}
              >
                <Avatar
                  src={getFullLinkImage(
                    "https://images.unsplash.com/photo-1664308703521-724bcdcdf39b?q=80&w=1972&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                  )}
                  icon="user"
                />

                <Col span={16} style={{ marginLeft: 12 }}>
                  <Row type="flex" justify="space-between" align="middle">
                    <Col>
                      <Typography className={styles.infoTitle}>BQL</Typography>
                    </Col>
                    <Typography className={styles.info}>
                      {detail.data &&
                        detail.data.created_at &&
                        moment.unix(1700738547).format("HH:mm, DD/MM/YYYY")}
                    </Typography>
                  </Row>
                  <Typography className={styles.info}>
                    {"Đã hủy thẻ"}
                  </Typography>
                </Col>
              </Row>
              <Row
                type="flex"
                align="middle"
                style={{
                  marginTop: 4,
                }}
              >
                <Avatar
                  src={getFullLinkImage(
                    "https://images.unsplash.com/photo-1664308703521-724bcdcdf39b?q=80&w=1972&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                  )}
                  icon="user"
                />

                <Col span={16} style={{ marginLeft: 12 }}>
                  <Row type="flex" justify="space-between" align="middle">
                    <Col>
                      <Typography className={styles.infoTitle}>BQL</Typography>
                    </Col>
                    <Typography className={styles.info}>
                      {detail.data &&
                        detail.data.created_at &&
                        moment.unix(1701638547).format("HH:mm, DD/MM/YYYY")}
                    </Typography>
                  </Row>
                  <Typography className={styles.info}>
                    {"Đã thu hồi thẻ"}
                  </Typography>
                </Col>
              </Row>
            </Col>
          </Row>
        </div>
      </Page>
    );
  }
}

const mapStateToProps = createStructuredSelector({
  combineCardDetail: makeSelectCombineCardDetail(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "combineCardDetail", reducer });
const withSaga = injectSaga({ key: "combineCardDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(CombineCardDetail));
