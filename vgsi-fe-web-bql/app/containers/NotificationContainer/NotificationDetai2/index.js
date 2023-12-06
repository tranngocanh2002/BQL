/**
 *
 * NotificationDetail
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { FormattedMessage } from "react-intl";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectNotificationDetail from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import messages from "./messages";
import Page from "../../../components/Page/Page";
import { Row, Col, Button, Upload } from "antd";
import { defaultAction, fetchDetailNotification, updateNotification } from "./actions";
import moment from "moment";
import { config } from "../../../utils";
import WithRole from "../../../components/WithRole";
import { getFullLinkImage } from "../../../connection";

/* eslint-disable react/prefer-stateless-function */
export class NotificationDetail extends React.PureComponent {

  constructor(props) {
    super(props)
    const { record } = props.location.state || {}
    this.state = {
      record
    }
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction())
  }


  componentDidMount() {
    const { id } = this.props.match.params
    if (id != undefined && !this.state.record) {
      this.props.dispatch(fetchDetailNotification({ id }))
    }
  }


  componentWillReceiveProps(nextProps) {
    if (this.props.notificationDetail.loading != nextProps.notificationDetail.loading && !nextProps.notificationDetail.loading) {
      const record = nextProps.notificationDetail.data

      this.setState({
        record,
      })
    }
  }



  render() {
    const { record } = this.state
    const { notificationDetail } = this.props
    console.log('record', record)
    return (
      <Page inner loading={!!!record} >
        <Row gutter={24} style={{ padding: 36 }} >
          <Col span={18} >
            <span style={{ color: '#1B1B27', fontSize: 28, fontWeight: 'bold' }} >{!!record && record.title}</span>
            <div dangerouslySetInnerHTML={{ __html: !!record && record.content }} style={{ marginTop: 24 }} />
            {
              !!record && !!record.attach && !!record.attach.fileImageList && record.attach.fileImageList.length > 0 && <Row style={{ marginTop: 24 }} >
                <Col style={{ fontWeight: 400, fontSize: 16, color: '#1B1B27', marginBottom: 8 }} >Ảnh đính kèm: </Col>
                <Upload
                  listType="picture-card"
                  fileList={!!record && !!record.attach && !!record.attach.fileImageList ? record.attach.fileImageList.map(img => ({
                    ...img,
                    url: getFullLinkImage(img.url)
                  })) : []}
                  onRemove={false}
                  onPreview={this.handlePreview}
                  onChange={this.handleChange}
                >
                </Upload>
              </Row>
            }
            {
              !!record && !!record.attach && !!record.attach.fileList && record.attach.fileList.length > 0 && <Row style={{ marginTop: 24 }} >
                <Col style={{ fontWeight: 400, fontSize: 16, color: '#1B1B27', marginBottom: 8 }} >File đính kèm: </Col>
                <Upload
                  fileList={!!record && !!record.attach && !!record.attach.fileList ? record.attach.fileList.map(img => ({
                    ...img,
                    url: getFullLinkImage(img.url)
                  })) : []}
                  onRemove={false}
                  onPreview={this.handlePreview}
                  onChange={this.handleChange}
                >
                </Upload>
              </Row>
            }
          </Col>
          <Col span={6} style={{ borderLeft: '1px solid #EFF1F4', paddingLeft: 16 }} >
            <Row type='flex' align='middle' >
              <span style={{ width: 120, color: '#A4A4AA' }} >Trạng thái:</span>
              <span className={!!record && record.status == 0 ? 'luci-status-warning' : 'luci-status-success'} style={{ width: 100, textAlign: 'center' }} >{(config.STATUS_NOTIFICATION.find(ii => !!record && ii.id == record.status) || {}).name}</span>
            </Row>
            <Row type='flex' align='middle' style={{ marginTop: 24 }} >
              <span style={{ width: 120, color: '#A4A4AA' }} >Ngày tạo:</span>
              <span style={{ color: '#1B1B27', fontWeight: 'bold' }} >{moment.unix(!!record ? record.created_at : undefined).format('DD/MM/YYYY - HH:mm')}</span>
            </Row>
            <Row type='flex' align='middle' style={{ marginTop: 24 }} >
              <span style={{ width: 120, color: '#A4A4AA' }} >Cập nhật:</span>
              <span style={{ color: '#1B1B27', fontWeight: 'bold' }} >{moment.unix(!!record ? record.updated_at : undefined).format('DD/MM/YYYY - HH:mm')}</span>
            </Row>
            <Row type='flex' align='middle' style={{ marginTop: 24 }} >
              <span style={{ width: 120, color: '#A4A4AA' }} >Danh mục:</span>
              <span style={{ color: '#1B1B27', fontWeight: 'bold' }} >{!!record ? record.announcement_category_name : ''}</span>
            </Row>
            <Row type='flex' align='middle' style={{ marginTop: 24 }} >
              <span style={{ width: 120, color: '#A4A4AA' }} >Căn hộ đã gửi:</span>
              <span style={{ color: '#1B1B27', fontWeight: 'bold' }} >{!!record ? record.total_apartment_send : 0}</span>
            </Row>
            <Row type='flex' align='middle' style={{ marginTop: 24 }} >
              <span style={{ width: 120, color: '#A4A4AA' }} >Lượt xem:</span>
              <span style={{ color: '#1B1B27', fontWeight: 'bold' }} >{!!record ? record.total_apartment_open : 0}</span>
            </Row>

            <WithRole roles={[config.ALL_ROLE_NAME.ANNOUNCE_CREATE_UPDATE]} >
              <Row type='flex' align='middle'  >
                <Button type='primary' style={{ marginRight: 10, marginTop: 24 }} disabled={notificationDetail.loading}
                  onClick={() => {
                    this.props.history.push(`/main/notification/edit/${record.id}`, {
                      record
                    })
                  }}
                >
                  <Row type='flex' align='middle'>
                    <i className="material-icons" style={{ fontSize: 20, marginRight: 6 }} >
                      edit
              </i><span>Sửa</span>
                  </Row>
                </Button>
                {
                  !!record && record.status == 0 &&
                  <Button type='primary' style={{
                    marginRight: 10, backgroundColor: '#F15A29',
                    border: '0px', marginTop: 24, display: 'flex'
                  }}
                    loading={notificationDetail.loading}
                    onClick={() => {
                      this.props.dispatch(updateNotification({
                        ...record,
                        status: 1
                      }))
                    }}
                  >
                    <Row type='flex' align='middle' style={{ marginLeft: 6 }}  >
                      {
                        !notificationDetail.loading && <i className="material-icons" style={{ fontSize: 20, marginRight: 6, transform: `rotate(-36deg)` }} >
                          send
                      </i>
                      }
                      <span>Công khai</span>
                    </Row>
                  </Button>
                }
              </Row>
            </WithRole>
          </Col>
        </Row>
      </Page>
    );
  }
}

NotificationDetail.propTypes = {
  dispatch: PropTypes.func.isRequired
};

const mapStateToProps = createStructuredSelector({
  notificationDetail: makeSelectNotificationDetail()
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch
  };
}

const withConnect = connect(
  mapStateToProps,
  mapDispatchToProps
);

const withReducer = injectReducer({ key: "notificationDetail", reducer });
const withSaga = injectSaga({ key: "notificationDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(NotificationDetail);
